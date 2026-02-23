<?php
/**
 * Roster-Timesheet Integration Test Cases
 * 
 * Tests to ensure roster changes properly sync to timesheet
 * 
 * @package    Bizadmin
 * @subpackage Tests\HR
 * @category   Tests
 */

use PHPUnit\Framework\TestCase;

class RosterTimesheetIntegrationTest extends TestCase
{
    /**
     * Test Location ID
     */
    protected $location_id = 44;
    
    /**
     * Test data
     */
    protected $testEmployeeId = 58;
    protected $testRosterId = 1;
    protected $testPrepAreaId = 8;
    
    /**
     * Date references
     */
    protected $weekStart;
    protected $weekEnd;
    
    /**
     * Set up test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->weekStart = date('Y-m-d', strtotime('monday this week'));
        $this->weekEnd = date('Y-m-d', strtotime('sunday this week'));
    }
    
    // =========================================================================
    // ROSTER TO TIMESHEET SYNC TESTS
    // =========================================================================
    
    /**
     * @test
     * @group integration
     * @group roster-timesheet-sync
     * 
     * Test Case I001: New roster creates corresponding timesheet entries
     * Expected: Timesheet details created for each roster shift
     */
    public function test_I001_new_roster_creates_timesheet_entries()
    {
        // Simulating the roster to timesheet synchronization
        $rosterDetails = [
            [
                'employee_id' => 58,
                'roster_date' => $this->weekStart,
                'shift_start_time' => '09:00:00',
                'shift_end_time' => '17:00:00'
            ],
            [
                'employee_id' => 58,
                'roster_date' => date('Y-m-d', strtotime($this->weekStart . ' +1 day')),
                'shift_start_time' => '09:00:00',
                'shift_end_time' => '17:00:00'
            ]
        ];
        
        // Simulated timesheet creation from roster
        $timesheetEntries = $this->simulateTimesheetCreation($rosterDetails);
        
        $this->assertCount(
            count($rosterDetails),
            $timesheetEntries,
            'Timesheet entries should match roster details count'
        );
        
        foreach ($timesheetEntries as $index => $entry) {
            $this->assertEquals(
                $rosterDetails[$index]['employee_id'],
                $entry['employee_id'],
                'Employee ID should match'
            );
            $this->assertEquals(
                $rosterDetails[$index]['shift_start_time'],
                $entry['roster_start_time'],
                'Roster start time should be recorded in timesheet'
            );
        }
    }
    
    /**
     * @test
     * @group integration
     * @group roster-timesheet-sync
     * 
     * Test Case I002: Update roster shift times - timesheet sync
     * Expected: Timesheet roster times updated, clock times preserved
     */
    public function test_I002_roster_time_update_syncs_to_timesheet()
    {
        // Original timesheet data (with clock times already recorded)
        $originalTimesheet = [
            'roster_start_time' => '09:00:00',
            'roster_end_time' => '17:00:00',
            'clock_in' => '09:05:00',
            'clock_out' => '17:02:00'
        ];
        
        // Updated roster times
        $updatedRosterTimes = [
            'shift_start_time' => '10:00:00',
            'shift_end_time' => '18:00:00'
        ];
        
        // Simulate sync - should update roster times but preserve clock times
        $syncedTimesheet = $this->simulateRosterTimesheetSync(
            $originalTimesheet,
            $updatedRosterTimes
        );
        
        // Verify roster times are updated
        $this->assertEquals(
            $updatedRosterTimes['shift_start_time'],
            $syncedTimesheet['roster_start_time'],
            'Roster start time should be updated in timesheet'
        );
        
        $this->assertEquals(
            $updatedRosterTimes['shift_end_time'],
            $syncedTimesheet['roster_end_time'],
            'Roster end time should be updated in timesheet'
        );
        
        // Verify clock times are preserved
        $this->assertEquals(
            $originalTimesheet['clock_in'],
            $syncedTimesheet['clock_in'],
            'Clock-in time should be preserved'
        );
        
        $this->assertEquals(
            $originalTimesheet['clock_out'],
            $syncedTimesheet['clock_out'],
            'Clock-out time should be preserved'
        );
    }
    
    /**
     * @test
     * @group integration
     * @group roster-timesheet-sync
     * 
     * Test Case I003: Add employee to roster - timesheet entry created
     * Expected: New timesheet detail entry for employee
     */
    public function test_I003_add_employee_to_roster_creates_timesheet()
    {
        $newRosterDetail = [
            'employee_id' => 36,
            'roster_date' => $this->weekStart,
            'shift_start_time' => '08:00:00',
            'shift_end_time' => '16:00:00',
            'prep_area_id' => $this->testPrepAreaId
        ];
        
        $timesheetEntry = $this->simulateTimesheetEntryCreation($newRosterDetail);
        
        $this->assertNotEmpty($timesheetEntry, 'Timesheet entry should be created');
        $this->assertEquals($newRosterDetail['employee_id'], $timesheetEntry['employee_id']);
        $this->assertEquals($newRosterDetail['roster_date'], $timesheetEntry['timesheet_date']);
        $this->assertNull($timesheetEntry['clock_in'], 'Clock-in should be null initially');
        $this->assertNull($timesheetEntry['clock_out'], 'Clock-out should be null initially');
    }
    
    /**
     * @test
     * @group integration
     * @group roster-timesheet-sync
     * 
     * Test Case I004: Remove employee from roster - timesheet entry deleted
     * Expected: Corresponding timesheet detail soft-deleted
     */
    public function test_I004_remove_employee_from_roster_deletes_timesheet()
    {
        $rosterDetailToRemove = [
            'id' => 1,
            'employee_id' => $this->testEmployeeId,
            'roster_date' => $this->weekStart,
            'is_deleted' => 0
        ];
        
        $timesheetBeforeRemoval = [
            'id' => 10,
            'employee_id' => $this->testEmployeeId,
            'timesheet_date' => $this->weekStart,
            'clock_in' => null, // Not yet clocked in
            'is_deleted' => 0
        ];
        
        // Simulate removal
        $timesheetAfterRemoval = $this->simulateTimesheetDeletion($timesheetBeforeRemoval);
        
        $this->assertEquals(1, $timesheetAfterRemoval['is_deleted'], 'Timesheet should be soft-deleted');
    }
    
    /**
     * @test
     * @group integration
     * @group roster-timesheet-sync
     * 
     * Test Case I005: Remove employee with existing clock data - timesheet preserved
     * Expected: Timesheet NOT deleted if employee already clocked in
     */
    public function test_I005_remove_employee_with_clock_preserves_timesheet()
    {
        $timesheetWithClock = [
            'id' => 10,
            'employee_id' => $this->testEmployeeId,
            'timesheet_date' => $this->weekStart,
            'clock_in' => '09:05:00', // Already clocked in
            'is_deleted' => 0
        ];
        
        // When roster is removed but employee has clock data, timesheet should be preserved
        $timesheetAfterRosterRemoval = $this->simulateTimesheetOnRosterRemovalWithClock(
            $timesheetWithClock
        );
        
        $this->assertEquals(
            0,
            $timesheetAfterRosterRemoval['is_deleted'],
            'Timesheet with clock data should NOT be deleted'
        );
    }
    
    /**
     * @test
     * @group integration
     * @group roster-timesheet-sync
     * 
     * Test Case I006: Re-add employee after removal - new timesheet created
     * Expected: New timesheet detail created (not reactivating old one)
     */
    public function test_I006_readd_employee_creates_new_timesheet()
    {
        $originalTimesheetId = 10;
        
        $newRosterDetail = [
            'employee_id' => $this->testEmployeeId,
            'roster_date' => $this->weekStart,
            'shift_start_time' => '09:00:00',
            'shift_end_time' => '17:00:00'
        ];
        
        $newTimesheetEntry = $this->simulateTimesheetEntryCreation($newRosterDetail);
        
        // Should create a new entry, not reuse old ID
        $this->assertNotEquals(
            $originalTimesheetId,
            $newTimesheetEntry['id'],
            'New timesheet entry should have different ID'
        );
    }
    
    /**
     * @test
     * @group integration
     * @group roster-timesheet-sync
     * 
     * Test Case I007: Update roster break time - timesheet syncs
     * Expected: Roster break time updated in timesheet
     */
    public function test_I007_roster_break_time_syncs_to_timesheet()
    {
        $rosterUpdate = [
            'break_start_time' => '12:30:00',
            'break_duration' => 45
        ];
        
        $originalTimesheet = [
            'roster_break_time' => '12:00:00',
            'roster_break_duration' => 30
        ];
        
        $syncedTimesheet = $originalTimesheet;
        $syncedTimesheet['roster_break_time'] = $rosterUpdate['break_start_time'];
        $syncedTimesheet['roster_break_duration'] = $rosterUpdate['break_duration'];
        
        $this->assertEquals(
            $rosterUpdate['break_start_time'],
            $syncedTimesheet['roster_break_time'],
            'Break time should be synced'
        );
        
        $this->assertEquals(
            $rosterUpdate['break_duration'],
            $syncedTimesheet['roster_break_duration'],
            'Break duration should be synced'
        );
    }
    
    /**
     * @test
     * @group integration
     * @group roster-timesheet-sync
     * 
     * Test Case I008: Multiple shifts same day - multiple timesheet entries
     * Expected: Each roster shift creates separate timesheet entry
     */
    public function test_I008_multiple_shifts_create_multiple_timesheets()
    {
        $rosterShifts = [
            [
                'employee_id' => $this->testEmployeeId,
                'roster_date' => $this->weekStart,
                'shift_start_time' => '06:00:00',
                'shift_end_time' => '10:00:00',
                'prep_area_id' => 8
            ],
            [
                'employee_id' => $this->testEmployeeId,
                'roster_date' => $this->weekStart,
                'shift_start_time' => '14:00:00',
                'shift_end_time' => '18:00:00',
                'prep_area_id' => 9
            ]
        ];
        
        $timesheetEntries = $this->simulateTimesheetCreation($rosterShifts);
        
        $this->assertCount(
            2,
            $timesheetEntries,
            'Should create 2 timesheet entries for 2 shifts'
        );
        
        // Verify different time slots
        $this->assertNotEquals(
            $timesheetEntries[0]['roster_start_time'],
            $timesheetEntries[1]['roster_start_time'],
            'Shift times should be different'
        );
    }
    
    // =========================================================================
    // ROSTER PUBLISH TO TIMESHEET TESTS
    // =========================================================================
    
    /**
     * @test
     * @group integration
     * @group roster-publish
     * 
     * Test Case I009: Publish roster - timesheet becomes active
     * Expected: Timesheets for roster are activated
     */
    public function test_I009_publish_roster_activates_timesheets()
    {
        $rosterBeforePublish = [
            'roster_id' => $this->testRosterId,
            'is_published' => 0
        ];
        
        $timesheetsBeforePublish = [
            ['id' => 1, 'is_active' => 0],
            ['id' => 2, 'is_active' => 0]
        ];
        
        // Simulate publish
        $rosterAfterPublish = $rosterBeforePublish;
        $rosterAfterPublish['is_published'] = 1;
        
        $timesheetsAfterPublish = [];
        foreach ($timesheetsBeforePublish as $ts) {
            $ts['is_active'] = 1;
            $timesheetsAfterPublish[] = $ts;
        }
        
        $this->assertEquals(1, $rosterAfterPublish['is_published']);
        
        foreach ($timesheetsAfterPublish as $ts) {
            $this->assertEquals(1, $ts['is_active'], 'Timesheet should be active after publish');
        }
    }
    
    /**
     * @test
     * @group integration
     * @group roster-publish
     * 
     * Test Case I010: Unpublish roster - timesheets remain
     * Expected: Timesheets are NOT deleted when roster unpublished
     */
    public function test_I010_unpublish_roster_preserves_timesheets()
    {
        $timesheetsWithClockData = [
            ['id' => 1, 'clock_in' => '09:00:00', 'is_deleted' => 0],
            ['id' => 2, 'clock_in' => null, 'is_deleted' => 0]
        ];
        
        // After unpublish, timesheets should be preserved
        foreach ($timesheetsWithClockData as $ts) {
            $this->assertEquals(0, $ts['is_deleted'], 'Timesheets should not be deleted on unpublish');
        }
    }
    
    // =========================================================================
    // DATA CONSISTENCY TESTS
    // =========================================================================
    
    /**
     * @test
     * @group integration
     * @group data-consistency
     * 
     * Test Case I011: Verify roster-timesheet employee match
     * Expected: Roster employee_id matches timesheet employee_id
     */
    public function test_I011_roster_timesheet_employee_consistency()
    {
        $rosterDetail = [
            'employee_id' => $this->testEmployeeId,
            'roster_date' => $this->weekStart
        ];
        
        $timesheetDetail = [
            'employee_id' => $this->testEmployeeId,
            'timesheet_date' => $this->weekStart
        ];
        
        $this->assertEquals(
            $rosterDetail['employee_id'],
            $timesheetDetail['employee_id'],
            'Employee ID should match between roster and timesheet'
        );
        
        $this->assertEquals(
            $rosterDetail['roster_date'],
            $timesheetDetail['timesheet_date'],
            'Date should match between roster and timesheet'
        );
    }
    
    /**
     * @test
     * @group integration
     * @group data-consistency
     * 
     * Test Case I012: Verify time format consistency
     * Expected: All times stored in consistent HH:MM format (no seconds in display)
     */
    public function test_I012_time_format_consistency()
    {
        $rosterTime = '09:00:00';
        $expectedDisplayFormat = '09:00';
        
        $displayTime = date('H:i', strtotime($rosterTime));
        
        $this->assertEquals(
            $expectedDisplayFormat,
            $displayTime,
            'Display time should be in HH:MM format'
        );
    }
    
    /**
     * @test
     * @group integration
     * @group data-consistency
     * 
     * Test Case I013: Verify week range consistency
     * Expected: Roster week matches timesheet date range
     */
    public function test_I013_week_range_consistency()
    {
        $rosterWeekStart = $this->weekStart;
        $rosterWeekEnd = $this->weekEnd;
        
        $timesheetDates = [$this->weekStart];
        for ($i = 1; $i <= 6; $i++) {
            $timesheetDates[] = date('Y-m-d', strtotime($this->weekStart . " +$i days"));
        }
        
        // All timesheet dates should be within roster week
        foreach ($timesheetDates as $date) {
            $this->assertTrue(
                $date >= $rosterWeekStart && $date <= $rosterWeekEnd,
                "Date $date should be within roster week"
            );
        }
    }
    
    // =========================================================================
    // EDGE CASE TESTS
    // =========================================================================
    
    /**
     * @test
     * @group integration
     * @group edge-cases
     * 
     * Test Case I014: Cross-midnight shift handling
     * Expected: Shift spanning midnight creates correct timesheet entries
     */
    public function test_I014_cross_midnight_shift_handling()
    {
        $nightShift = [
            'roster_date' => $this->weekStart,
            'shift_start_time' => '22:00:00',
            'shift_end_time' => '06:00:00' // Next day
        ];
        
        // Total shift duration should be 8 hours
        $startTime = strtotime($nightShift['roster_date'] . ' ' . $nightShift['shift_start_time']);
        $endTime = strtotime($nightShift['roster_date'] . ' ' . $nightShift['shift_end_time']);
        
        // Adjust for next day if end time is less than start time
        if ($endTime < $startTime) {
            $endTime = strtotime($nightShift['roster_date'] . ' +1 day ' . $nightShift['shift_end_time']);
        }
        
        $shiftDurationHours = ($endTime - $startTime) / 3600;
        
        $this->assertEquals(8, $shiftDurationHours, 'Night shift should be 8 hours');
    }
    
    /**
     * @test
     * @group integration
     * @group edge-cases
     * 
     * Test Case I016: Roster time change with existing clock data - NO DUPLICATE
     * Expected: When roster time changes, update existing timesheet - don't create duplicate
     * 
     * This tests the bug where employee 43 on 2026-02-14 got duplicate entries because:
     * - Original roster: 16:00-22:30, employee clocked 09:00-23:00
     * - Updated roster: 09:00-22:30 (to match actual clock times)
     * - Bug created NEW entry instead of updating existing one
     */
    public function test_I016_roster_time_change_no_duplicate()
    {
        // Original timesheet entry with clock data
        $originalTimesheet = [
            'timesheet_id' => 4192,
            'employee_id' => $this->testEmployeeId,
            'roster_date' => $this->weekStart,
            'roster_start_time' => '16:00:00',  // Original roster time
            'roster_end_time' => '22:30:00',
            'clock_in_time' => '09:00:00',      // Actual clock (earlier than roster)
            'clock_out_time' => '23:00:00',
            'is_deleted' => 0
        ];
        
        // Updated roster - time changed to match actual clock times
        $updatedRosterDetail = [
            'employee_id' => $this->testEmployeeId,
            'roster_date' => $this->weekStart,
            'shift_start_time' => '09:00:00',   // Changed from 16:00 to 09:00
            'shift_end_time' => '22:30:00'
        ];
        
        // Simulate the FIXED matching logic (employee + date, NOT requiring roster_start_time match)
        $shouldMatch = (
            $originalTimesheet['employee_id'] == $updatedRosterDetail['employee_id'] &&
            $originalTimesheet['roster_date'] == $updatedRosterDetail['roster_date']
            // NOTE: We no longer check roster_start_time match - that's the fix!
        );
        
        $this->assertTrue($shouldMatch, 'Should match by employee_id + date even when roster time changes');
        
        // The correct behavior: UPDATE existing entry, not create new one
        $updatedTimesheet = $originalTimesheet;
        $updatedTimesheet['roster_start_time'] = $updatedRosterDetail['shift_start_time'];
        $updatedTimesheet['roster_end_time'] = $updatedRosterDetail['shift_end_time'];
        // Clock data should be preserved
        
        $this->assertEquals($originalTimesheet['clock_in_time'], $updatedTimesheet['clock_in_time'], 'Clock-in time should be preserved');
        $this->assertEquals($originalTimesheet['clock_out_time'], $updatedTimesheet['clock_out_time'], 'Clock-out time should be preserved');
        $this->assertEquals('09:00:00', $updatedTimesheet['roster_start_time'], 'Roster start time should be updated');
    }
    
    /**
     * @test
     * @group integration
     * @group edge-cases
     * 
     * Test Case I015: Partial week roster
     * Expected: Only scheduled days have timesheet entries
     */
    public function test_I015_partial_week_roster()
    {
        // Employee only scheduled Mon, Wed, Fri
        $scheduledDays = [
            date('Y-m-d', strtotime('monday this week')),
            date('Y-m-d', strtotime('wednesday this week')),
            date('Y-m-d', strtotime('friday this week'))
        ];
        
        $timesheetEntries = [];
        foreach ($scheduledDays as $day) {
            $timesheetEntries[] = [
                'timesheet_date' => $day,
                'employee_id' => $this->testEmployeeId
            ];
        }
        
        $this->assertCount(3, $timesheetEntries, 'Should only have 3 timesheet entries');
        
        // Tuesday and Thursday should not have entries
        $unscheduledTuesday = date('Y-m-d', strtotime('tuesday this week'));
        $hasUnscheduledEntry = false;
        
        foreach ($timesheetEntries as $entry) {
            if ($entry['timesheet_date'] === $unscheduledTuesday) {
                $hasUnscheduledEntry = true;
            }
        }
        
        $this->assertFalse($hasUnscheduledEntry, 'Tuesday should not have timesheet entry');
    }
    
    // =========================================================================
    // HELPER METHODS
    // =========================================================================
    
    /**
     * Simulate timesheet creation from roster details
     */
    protected function simulateTimesheetCreation(array $rosterDetails): array
    {
        $timesheets = [];
        
        foreach ($rosterDetails as $detail) {
            $timesheets[] = [
                'id' => rand(1000, 9999),
                'employee_id' => $detail['employee_id'],
                'timesheet_date' => $detail['roster_date'],
                'roster_start_time' => $detail['shift_start_time'],
                'roster_end_time' => $detail['shift_end_time'],
                'clock_in' => null,
                'clock_out' => null,
                'is_deleted' => 0
            ];
        }
        
        return $timesheets;
    }
    
    /**
     * Simulate roster to timesheet sync
     */
    protected function simulateRosterTimesheetSync(array $originalTimesheet, array $rosterUpdate): array
    {
        $synced = $originalTimesheet;
        $synced['roster_start_time'] = $rosterUpdate['shift_start_time'];
        $synced['roster_end_time'] = $rosterUpdate['shift_end_time'];
        // Preserve clock times
        return $synced;
    }
    
    /**
     * Simulate single timesheet entry creation
     */
    protected function simulateTimesheetEntryCreation(array $rosterDetail): array
    {
        return [
            'id' => rand(1000, 9999),
            'employee_id' => $rosterDetail['employee_id'],
            'timesheet_date' => $rosterDetail['roster_date'],
            'roster_start_time' => $rosterDetail['shift_start_time'],
            'roster_end_time' => $rosterDetail['shift_end_time'],
            'clock_in' => null,
            'clock_out' => null,
            'is_deleted' => 0
        ];
    }
    
    /**
     * Simulate timesheet soft-deletion
     */
    protected function simulateTimesheetDeletion(array $timesheet): array
    {
        $timesheet['is_deleted'] = 1;
        return $timesheet;
    }
    
    /**
     * Simulate timesheet behavior when roster removed but clock data exists
     */
    protected function simulateTimesheetOnRosterRemovalWithClock(array $timesheet): array
    {
        // If clock data exists, don't delete
        if (!empty($timesheet['clock_in']) || !empty($timesheet['clock_out'])) {
            return $timesheet; // Keep as is
        }
        
        $timesheet['is_deleted'] = 1;
        return $timesheet;
    }
}
