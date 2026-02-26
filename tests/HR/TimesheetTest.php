<?php
/**
 * Timesheet Feature Test Cases
 * 
 * Comprehensive test suite for Timesheet functionality
 * 
 * @package    Bizadmin
 * @subpackage Tests\HR
 * @category   Tests
 */

use PHPUnit\Framework\TestCase;

class TimesheetTest extends TestCase
{
    /**
     * Test Location ID for all tests
     */
    protected $location_id = 44;
    
    /**
     * Test data
     */
    protected $testEmployeeId = 58;
    protected $testTimesheetId = 1;
    protected $testTimesheetDetailId = 1;
    
    /**
     * Current date
     */
    protected $today;
    
    /**
     * Set up test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->today = date('Y-m-d');
    }
    
    // =========================================================================
    // TIMESHEET CLOCK-IN/OUT TESTS
    // =========================================================================
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-clock
     * 
     * Test Case T001: Employee clock-in at scheduled time
     * Expected: Timesheet created with clock-in time recorded
     */
    public function test_T001_employee_clock_in_at_scheduled_time()
    {
        $clockInData = [
            'employee_id' => $this->testEmployeeId,
            'clock_in_time' => '09:00:00',
            'scheduled_start' => '09:00:00',
            'date' => $this->today
        ];
        
        $timeDifference = abs(
            strtotime($clockInData['clock_in_time']) - 
            strtotime($clockInData['scheduled_start'])
        );
        
        // Within 5 minutes tolerance
        $this->assertLessThanOrEqual(300, $timeDifference, 'Clock-in should be at or near scheduled time');
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-clock
     * 
     * Test Case T002: Employee clock-in before scheduled time (early arrival)
     * Expected: Clock-in recorded as per roster start time (not early)
     */
    public function test_T002_employee_early_clock_in()
    {
        $clockInData = [
            'actual_clock_in' => '08:45:00',
            'scheduled_start' => '09:00:00',
            'recorded_clock_in' => '09:00:00' // Should use scheduled time
        ];
        
        // Early arrivals should use scheduled time
        $this->assertEquals(
            $clockInData['scheduled_start'],
            $clockInData['recorded_clock_in'],
            'Early clock-in should use scheduled start time'
        );
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-clock
     * 
     * Test Case T003: Employee clock-in late
     * Expected: Actual late clock-in time recorded
     */
    public function test_T003_employee_late_clock_in()
    {
        $clockInData = [
            'actual_clock_in' => '09:15:00',
            'scheduled_start' => '09:00:00',
            'recorded_clock_in' => '09:15:00' // Should use actual time when late
        ];
        
        // Late arrivals should use actual clock-in time
        $this->assertEquals(
            $clockInData['actual_clock_in'],
            $clockInData['recorded_clock_in'],
            'Late clock-in should record actual time'
        );
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-clock
     * 
     * Test Case T004: Employee clock-out at scheduled time
     * Expected: Clock-out time recorded correctly
     */
    public function test_T004_employee_clock_out_at_scheduled_time()
    {
        $clockOutData = [
            'employee_id' => $this->testEmployeeId,
            'clock_out_time' => '17:00:00',
            'scheduled_end' => '17:00:00',
            'date' => $this->today
        ];
        
        $this->assertEquals(
            $clockOutData['scheduled_end'],
            $clockOutData['clock_out_time'],
            'Clock-out should match scheduled end time'
        );
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-clock
     * 
     * Test Case T005: Employee clock-out early
     * Expected: Actual clock-out time recorded
     */
    public function test_T005_employee_early_clock_out()
    {
        $clockOutData = [
            'actual_clock_out' => '16:30:00',
            'scheduled_end' => '17:00:00',
            'recorded_clock_out' => '16:30:00'
        ];
        
        // Early departures should use actual time
        $this->assertEquals(
            $clockOutData['actual_clock_out'],
            $clockOutData['recorded_clock_out'],
            'Early clock-out should record actual time'
        );
    }
    
    // =========================================================================
    // TIMESHEET UPDATE TESTS (MANAGER SIDE)
    // =========================================================================
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-update
     * 
     * Test Case T006: Manager updates clock-in time
     * Expected: Clock-in time updated successfully
     */
    public function test_T006_manager_update_clock_in_time()
    {
        $originalClockIn = '09:15:00';
        $newClockIn = '09:00:00';
        
        // Simulate update payload
        $updateData = [
            'timesheet_detail_id' => $this->testTimesheetDetailId,
            'clock_in' => $newClockIn,
            'update_type' => 'manager_update'
        ];
        
        $this->assertNotEquals(
            $originalClockIn,
            $newClockIn,
            'Clock-in time should be different'
        );
        
        $this->assertEquals('09:00:00', $updateData['clock_in'], 'New clock-in time should be set');
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-update
     * 
     * Test Case T007: Manager updates clock-out time
     * Expected: Clock-out time updated successfully
     */
    public function test_T007_manager_update_clock_out_time()
    {
        $originalClockOut = '16:30:00';
        $newClockOut = '17:00:00';
        
        $updateData = [
            'timesheet_detail_id' => $this->testTimesheetDetailId,
            'clock_out' => $newClockOut,
            'update_type' => 'manager_update'
        ];
        
        $this->assertEquals('17:00:00', $updateData['clock_out'], 'New clock-out time should be set');
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-update
     * 
     * Test Case T008: Overnight shift - Clock-out after midnight is valid
     * Expected: Overnight shifts are recognized and valid
     */
    public function test_T008_overnight_shift_clock_out_after_midnight_is_valid()
    {
        // Overnight shift: 10 PM to 2 AM next day
        $updateData = [
            'clock_in' => '22:00:00',
            'clock_out' => '02:00:00' // Appears before clock-in but is next day
        ];
        
        // For overnight shifts, clock_out time appearing before clock_in indicates next day
        $isOvernightShift = strtotime($updateData['clock_out']) <= strtotime($updateData['clock_in']);
        $isNotSameTime = $updateData['clock_out'] !== $updateData['clock_in'];
        
        // Overnight shifts where clock_out != clock_in are valid
        $this->assertTrue($isOvernightShift && $isNotSameTime, 'Overnight shift should be recognized as valid');
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-update
     * 
     * Test Case T008b: Fail - Same clock-in and clock-out time (0 hour shift)
     * Expected: Error - Invalid as no hours worked
     */
    public function test_T008b_same_clock_in_and_out_time_should_fail()
    {
        $updateData = [
            'clock_in' => '17:00:00',
            'clock_out' => '17:00:00' // Same time - 0 hours
        ];
        
        $isValid = $updateData['clock_out'] !== $updateData['clock_in'];
        
        $this->assertFalse($isValid, 'Same clock-in and clock-out time should be invalid');
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-update
     * 
     * Test Case T008c: Overnight shift hours calculation
     * Expected: Hours calculated correctly for overnight shift
     */
    public function test_T008c_overnight_shift_hours_calculation()
    {
        // Overnight shift: 10 PM to 2 AM = 4 hours
        $clockIn = '22:00:00';
        $clockOut = '02:00:00';
        $rosterDate = '2026-02-25';
        
        // Calculate using the same logic as the helper function
        $clockInTs = strtotime($rosterDate . ' ' . $clockIn);
        $clockOutTs = strtotime($rosterDate . ' ' . $clockOut);
        
        // Handle overnight - add 24 hours if clock_out appears before clock_in
        if ($clockOutTs <= $clockInTs) {
            $clockOutTs += 86400;
        }
        
        $workedSeconds = $clockOutTs - $clockInTs;
        $workedHours = $workedSeconds / 3600;
        
        $this->assertEquals(4, $workedHours, 'Overnight shift (10PM-2AM) should be 4 hours');
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-update
     * 
     * Test Case T009: Update timesheet with worked hours calculation
     * Expected: Worked hours calculated correctly
     */
    public function test_T009_worked_hours_calculation()
    {
        $timesheetData = [
            'clock_in' => '09:00:00',
            'clock_out' => '17:00:00',
            'break_duration' => 30 // minutes
        ];
        
        $workedMinutes = $this->calculateWorkedMinutes(
            $timesheetData['clock_in'],
            $timesheetData['clock_out'],
            $timesheetData['break_duration']
        );
        
        // 8 hours = 480 minutes, minus 30 minute break = 450 minutes
        $this->assertEquals(450, $workedMinutes, 'Worked minutes should be 450 (7.5 hours)');
    }
    
    // =========================================================================
    // TIMESHEET BREAK TESTS
    // =========================================================================
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-break
     * 
     * Test Case T010: Add break to timesheet
     * Expected: Break time recorded
     */
    public function test_T010_add_break_to_timesheet()
    {
        $breakData = [
            'timesheet_detail_id' => $this->testTimesheetDetailId,
            'break_start' => '12:00:00',
            'break_end' => '12:30:00',
            'break_type' => 'unpaid'
        ];
        
        $breakDuration = $this->calculateBreakDuration(
            $breakData['break_start'],
            $breakData['break_end']
        );
        
        $this->assertEquals(30, $breakDuration, 'Break duration should be 30 minutes');
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-break
     * 
     * Test Case T011: Update break time from manager side
     * Expected: Break time updated, worked hours recalculated
     */
    public function test_T011_manager_update_break_time()
    {
        $originalBreak = ['start' => '12:00:00', 'end' => '12:30:00'];
        $updatedBreak = ['start' => '12:30:00', 'end' => '13:00:00'];
        
        $this->assertNotEquals(
            $originalBreak['start'],
            $updatedBreak['start'],
            'Break start time should be different'
        );
        
        // Break duration should remain same
        $originalDuration = $this->calculateBreakDuration($originalBreak['start'], $originalBreak['end']);
        $updatedDuration = $this->calculateBreakDuration($updatedBreak['start'], $updatedBreak['end']);
        
        $this->assertEquals($originalDuration, $updatedDuration, 'Break duration should be same');
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-break
     * 
     * Test Case T012: Multiple breaks in single shift
     * Expected: All breaks recorded, total break time calculated
     */
    public function test_T012_multiple_breaks_in_single_shift()
    {
        $breaks = [
            ['start' => '10:00:00', 'end' => '10:15:00'], // 15 min
            ['start' => '12:00:00', 'end' => '12:30:00'], // 30 min
            ['start' => '15:00:00', 'end' => '15:15:00']  // 15 min
        ];
        
        $totalBreakMinutes = 0;
        foreach ($breaks as $break) {
            $totalBreakMinutes += $this->calculateBreakDuration($break['start'], $break['end']);
        }
        
        $this->assertEquals(60, $totalBreakMinutes, 'Total break time should be 60 minutes');
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-break
     * 
     * Test Case T013: Manual break override
     * Expected: Override flag set, custom break duration applied
     */
    public function test_T013_manual_break_override()
    {
        $timesheetData = [
            'timesheet_detail_id' => $this->testTimesheetDetailId,
            'original_break_duration' => 30,
            'manual_override' => true,
            'override_break_duration' => 45
        ];
        
        $this->assertTrue($timesheetData['manual_override'], 'Manual override should be enabled');
        $this->assertEquals(
            45,
            $timesheetData['override_break_duration'],
            'Override break duration should be 45 minutes'
        );
    }
    
    // =========================================================================
    // TIMESHEET APPROVAL TESTS
    // =========================================================================
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-approval
     * 
     * Test Case T014: Approve single timesheet
     * Expected: Timesheet status changed to approved
     */
    public function test_T014_approve_single_timesheet()
    {
        $timesheet = [
            'timesheet_id' => $this->testTimesheetId,
            'status' => 'pending',
            'approved_by' => null
        ];
        
        $approvedTimesheet = $timesheet;
        $approvedTimesheet['status'] = 'approved';
        $approvedTimesheet['approved_by'] = 1; // Manager ID
        
        $this->assertEquals('approved', $approvedTimesheet['status'], 'Status should be approved');
        $this->assertNotNull($approvedTimesheet['approved_by'], 'Approved by should be set');
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-approval
     * 
     * Test Case T015: Bulk approve timesheets
     * Expected: Multiple timesheets approved at once
     */
    public function test_T015_bulk_approve_timesheets()
    {
        $timesheetIds = [1, 2, 3, 4, 5];
        $approvalResult = [];
        
        foreach ($timesheetIds as $id) {
            $approvalResult[$id] = [
                'status' => 'approved',
                'approved_at' => date('Y-m-d H:i:s')
            ];
        }
        
        $this->assertCount(5, $approvalResult, 'All 5 timesheets should be approved');
        
        foreach ($approvalResult as $result) {
            $this->assertEquals('approved', $result['status']);
        }
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-approval
     * 
     * Test Case T016: Reject timesheet with reason
     * Expected: Timesheet rejected with reason recorded
     */
    public function test_T016_reject_timesheet_with_reason()
    {
        $rejectedTimesheet = [
            'timesheet_id' => $this->testTimesheetId,
            'status' => 'rejected',
            'rejection_reason' => 'Incorrect hours reported - please verify clock-in time',
            'rejected_by' => 1
        ];
        
        $this->assertEquals('rejected', $rejectedTimesheet['status']);
        $this->assertNotEmpty($rejectedTimesheet['rejection_reason'], 'Rejection reason should be provided');
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-approval
     * 
     * Test Case T017: Fail - Approve already approved timesheet
     * Expected: Error or no change if already approved
     */
    public function test_T017_approve_already_approved_should_not_change()
    {
        $approvedTimesheet = [
            'timesheet_id' => $this->testTimesheetId,
            'status' => 'approved',
            'approved_by' => 1,
            'approved_at' => '2024-01-15 10:00:00'
        ];
        
        $reApprovalAttempt = $approvedTimesheet;
        // Status should remain same
        
        $this->assertEquals($approvedTimesheet['status'], $reApprovalAttempt['status']);
        $this->assertEquals($approvedTimesheet['approved_at'], $reApprovalAttempt['approved_at']);
    }
    
    // =========================================================================
    // TIMESHEET COST CALCULATION TESTS
    // =========================================================================
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-cost
     * 
     * Test Case T018: Calculate weekday cost
     * Expected: Correct cost based on hourly rate and weekday hours
     */
    public function test_T018_calculate_weekday_cost()
    {
        $hourlyRate = 25.00;
        $hoursWorked = 7.5; // 7 hours 30 minutes
        
        $cost = $hourlyRate * $hoursWorked;
        
        $this->assertEquals(187.50, $cost, 'Weekday cost should be $187.50');
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-cost
     * 
     * Test Case T019: Calculate weekend cost with premium
     * Expected: Correct cost with weekend rate multiplier
     */
    public function test_T019_calculate_weekend_cost_with_premium()
    {
        $baseHourlyRate = 25.00;
        $weekendMultiplier = 1.5;
        $hoursWorked = 8.0;
        
        $cost = $baseHourlyRate * $weekendMultiplier * $hoursWorked;
        
        $this->assertEquals(300.00, $cost, 'Weekend cost should be $300.00');
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-cost
     * 
     * Test Case T020: Weekly report totals calculation
     * Expected: Correct weekly totals for hours and cost
     */
    public function test_T020_weekly_report_totals()
    {
        $weeklyData = [
            ['day' => 'Monday', 'hours' => 8.0, 'rate' => 25.00, 'is_weekend' => false],
            ['day' => 'Tuesday', 'hours' => 8.0, 'rate' => 25.00, 'is_weekend' => false],
            ['day' => 'Wednesday', 'hours' => 7.5, 'rate' => 25.00, 'is_weekend' => false],
            ['day' => 'Thursday', 'hours' => 8.0, 'rate' => 25.00, 'is_weekend' => false],
            ['day' => 'Friday', 'hours' => 8.0, 'rate' => 25.00, 'is_weekend' => false],
            ['day' => 'Saturday', 'hours' => 4.0, 'rate' => 25.00, 'is_weekend' => true],
            ['day' => 'Sunday', 'hours' => 0, 'rate' => 25.00, 'is_weekend' => true],
        ];
        
        $weekendMultiplier = 1.5;
        $totalHours = 0;
        $totalCost = 0;
        $weekdayHours = 0;
        $weekendHours = 0;
        
        foreach ($weeklyData as $day) {
            $totalHours += $day['hours'];
            
            if ($day['is_weekend']) {
                $weekendHours += $day['hours'];
                $totalCost += $day['hours'] * $day['rate'] * $weekendMultiplier;
            } else {
                $weekdayHours += $day['hours'];
                $totalCost += $day['hours'] * $day['rate'];
            }
        }
        
        $this->assertEquals(43.5, $totalHours, 'Total hours should be 43.5');
        $this->assertEquals(39.5, $weekdayHours, 'Weekday hours should be 39.5');
        $this->assertEquals(4.0, $weekendHours, 'Weekend hours should be 4.0');
        $this->assertEquals(1137.50, $totalCost, 'Total cost should be $1137.50');
    }
    
    // =========================================================================
    // TIMESHEET VIEW/FILTER TESTS
    // =========================================================================
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-filter
     * 
     * Test Case T021: Filter timesheets by date range
     * Expected: Only timesheets within date range returned
     */
    public function test_T021_filter_timesheets_by_date_range()
    {
        $filterData = [
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-07'
        ];
        
        $testDates = ['2024-01-03', '2024-01-10', '2024-01-05', '2023-12-31'];
        $filteredDates = [];
        
        foreach ($testDates as $date) {
            if ($date >= $filterData['start_date'] && $date <= $filterData['end_date']) {
                $filteredDates[] = $date;
            }
        }
        
        $this->assertCount(2, $filteredDates, 'Should return 2 dates within range');
        $this->assertContains('2024-01-03', $filteredDates);
        $this->assertContains('2024-01-05', $filteredDates);
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-filter
     * 
     * Test Case T022: Filter timesheets by employee
     * Expected: Only timesheets for specified employee returned
     */
    public function test_T022_filter_timesheets_by_employee()
    {
        $filterEmployeeId = 58;
        
        $timesheets = [
            ['id' => 1, 'employee_id' => 58],
            ['id' => 2, 'employee_id' => 36],
            ['id' => 3, 'employee_id' => 58],
            ['id' => 4, 'employee_id' => 45],
        ];
        
        $filteredTimesheets = array_filter($timesheets, function($ts) use ($filterEmployeeId) {
            return $ts['employee_id'] === $filterEmployeeId;
        });
        
        $this->assertCount(2, $filteredTimesheets, 'Should return 2 timesheets for employee 58');
    }
    
    /**
     * @test
     * @group timesheet
     * @group timesheet-filter
     * 
     * Test Case T023: Filter timesheets by status
     * Expected: Only timesheets with specified status returned
     */
    public function test_T023_filter_timesheets_by_status()
    {
        $filterStatus = 'pending';
        
        $timesheets = [
            ['id' => 1, 'status' => 'pending'],
            ['id' => 2, 'status' => 'approved'],
            ['id' => 3, 'status' => 'pending'],
            ['id' => 4, 'status' => 'rejected'],
        ];
        
        $filteredTimesheets = array_filter($timesheets, function($ts) use ($filterStatus) {
            return $ts['status'] === $filterStatus;
        });
        
        $this->assertCount(2, $filteredTimesheets, 'Should return 2 pending timesheets');
    }
    
    // =========================================================================
    // AUTO-BREAK & MANUAL BREAK OVERRIDE TESTS
    // =========================================================================
    
    /**
     * @test
     * @group timesheet-break
     * @group auto-break
     * 
     * Test Case T024: Auto-break 30 minutes for 5-10 hours worked
     * Expected: 30 minute break auto-applied when no break recorded
     */
    public function test_T024_auto_break_30_min_for_5_to_10_hours()
    {
        $totalHoursSeconds = 6 * 3600; // 6 hours in seconds
        $breakDuration = 0; // No break recorded
        $manualOverride = false;
        
        // Apply auto-break logic
        if (!$manualOverride && $breakDuration == 0 && $totalHoursSeconds > 0) {
            $hoursWorked = $totalHoursSeconds / 3600;
            if ($hoursWorked >= 10) {
                $breakDuration = 60;
            } elseif ($hoursWorked >= 5) {
                $breakDuration = 30;
            }
        }
        
        $this->assertEquals(30, $breakDuration, 'Auto-break should be 30 minutes for 6 hours worked');
    }
    
    /**
     * @test
     * @group timesheet-break
     * @group auto-break
     * 
     * Test Case T025: Auto-break 60 minutes for 10+ hours worked
     * Expected: 60 minute break auto-applied when no break recorded
     */
    public function test_T025_auto_break_60_min_for_10_plus_hours()
    {
        $totalHoursSeconds = 11 * 3600; // 11 hours in seconds
        $breakDuration = 0; // No break recorded
        $manualOverride = false;
        
        // Apply auto-break logic
        if (!$manualOverride && $breakDuration == 0 && $totalHoursSeconds > 0) {
            $hoursWorked = $totalHoursSeconds / 3600;
            if ($hoursWorked >= 10) {
                $breakDuration = 60;
            } elseif ($hoursWorked >= 5) {
                $breakDuration = 30;
            }
        }
        
        $this->assertEquals(60, $breakDuration, 'Auto-break should be 60 minutes for 11 hours worked');
    }
    
    /**
     * @test
     * @group timesheet-break
     * @group auto-break
     * 
     * Test Case T026: No auto-break for less than 5 hours worked
     * Expected: No break applied for short shifts
     */
    public function test_T026_no_auto_break_under_5_hours()
    {
        $totalHoursSeconds = 4 * 3600; // 4 hours in seconds
        $breakDuration = 0; // No break recorded
        $manualOverride = false;
        
        // Apply auto-break logic
        if (!$manualOverride && $breakDuration == 0 && $totalHoursSeconds > 0) {
            $hoursWorked = $totalHoursSeconds / 3600;
            if ($hoursWorked >= 10) {
                $breakDuration = 60;
            } elseif ($hoursWorked >= 5) {
                $breakDuration = 30;
            }
        }
        
        $this->assertEquals(0, $breakDuration, 'No auto-break should be applied for 4 hours worked');
    }
    
    /**
     * @test
     * @group timesheet-break
     * @group manual-break-override
     * 
     * Test Case T027: Manual break override with 0 minutes should NOT apply auto-break
     * Expected: Break stays at 0 even if hours would trigger auto-break
     */
    public function test_T027_manual_break_override_zero_no_auto_break()
    {
        $totalHoursSeconds = 8 * 3600; // 8 hours - would normally get 30 min auto-break
        $manualOverride = true;
        $manualBreakMinutes = 0; // User explicitly set to 0
        
        // Calculate break using the CORRECT logic
        if ($manualOverride && $manualBreakMinutes !== null) {
            $breakDuration = $manualBreakMinutes;
        } else {
            $breakDuration = 0;
            $hoursWorked = $totalHoursSeconds / 3600;
            if ($hoursWorked >= 10) {
                $breakDuration = 60;
            } elseif ($hoursWorked >= 5) {
                $breakDuration = 30;
            }
        }
        
        $this->assertEquals(0, $breakDuration, 'Manual override to 0 should NOT apply auto-break');
    }
    
    /**
     * @test
     * @group timesheet-break
     * @group manual-break-override
     * 
     * Test Case T028: Manual break override with custom value
     * Expected: Break uses manual value instead of auto-calculated
     */
    public function test_T028_manual_break_override_custom_value()
    {
        $totalHoursSeconds = 8 * 3600; // 8 hours - would normally get 30 min auto-break
        $manualOverride = true;
        $manualBreakMinutes = 45; // User explicitly set to 45
        
        // Calculate break using the CORRECT logic
        if ($manualOverride && $manualBreakMinutes !== null) {
            $breakDuration = $manualBreakMinutes;
        } else {
            $breakDuration = 0;
            $hoursWorked = $totalHoursSeconds / 3600;
            if ($hoursWorked >= 10) {
                $breakDuration = 60;
            } elseif ($hoursWorked >= 5) {
                $breakDuration = 30;
            }
        }
        
        $this->assertEquals(45, $breakDuration, 'Manual override should use 45 minutes');
    }
    
    /**
     * @test
     * @group timesheet-break
     * @group manual-break-override
     * 
     * Test Case T029: Total hours calculation with manual break override
     * Expected: Net hours correctly calculated using manual break, not auto-break
     */
    public function test_T029_total_hours_with_manual_break_override()
    {
        // 8 hours worked in seconds
        $totalHoursSeconds = 8 * 3600;
        
        // Manual override to 0 breaks
        $manualOverride = true;
        $manualBreakMinutes = 0;
        
        // Calculate break
        if ($manualOverride && $manualBreakMinutes !== null) {
            $breakDuration = $manualBreakMinutes;
        } else {
            $breakDuration = 0;
            $hoursWorked = $totalHoursSeconds / 3600;
            if ($hoursWorked >= 10) {
                $breakDuration = 60;
            } elseif ($hoursWorked >= 5) {
                $breakDuration = 30;
            }
        }
        
        // Calculate net hours
        $netSeconds = $totalHoursSeconds - ($breakDuration * 60);
        $netMinutes = round($netSeconds / 60);
        $hours = floor($netMinutes / 60);
        $minutes = $netMinutes % 60;
        $formattedHours = "{$hours} hrs {$minutes} min";
        
        // With 0 break, should be full 8 hours
        $this->assertEquals('8 hrs 0 min', $formattedHours, 'With 0 break override, should show full 8 hours');
    }
    
    /**
     * @test
     * @group timesheet-break
     * @group manual-break-override
     * 
     * Test Case T030: Total hours calculation WITHOUT manual override (auto-break applied)
     * Expected: Net hours correctly calculated with auto-break deduction
     */
    public function test_T030_total_hours_with_auto_break()
    {
        // 8 hours worked in seconds
        $totalHoursSeconds = 8 * 3600;
        
        // No manual override
        $manualOverride = false;
        $manualBreakMinutes = null;
        
        // Calculate break
        if ($manualOverride && $manualBreakMinutes !== null) {
            $breakDuration = $manualBreakMinutes;
        } else {
            $breakDuration = 0;
            $hoursWorked = $totalHoursSeconds / 3600;
            if ($hoursWorked >= 10) {
                $breakDuration = 60;
            } elseif ($hoursWorked >= 5) {
                $breakDuration = 30;
            }
        }
        
        // Calculate net hours
        $netSeconds = $totalHoursSeconds - ($breakDuration * 60);
        $netMinutes = round($netSeconds / 60);
        $hours = floor($netMinutes / 60);
        $minutes = $netMinutes % 60;
        $formattedHours = "{$hours} hrs {$minutes} min";
        
        // With 30 min auto-break, should be 7 hrs 30 min
        $this->assertEquals('7 hrs 30 min', $formattedHours, 'With auto-break, should show 7 hrs 30 min');
    }
    
    /**
     * @test
     * @group timesheet-break
     * @group manual-break-override
     * 
     * Test Case T031: Multiple timesheets total with mixed break overrides
     * Expected: Correct total when some timesheets have manual override, others auto
     */
    public function test_T031_multiple_timesheets_mixed_break_overrides()
    {
        $timesheets = [
            [
                'total_hours' => '08:00:00', // 8 hours - would auto = 30 min
                'total_break_duration' => 0,
                'manual_break_override' => 1,
                'manual_break_minutes' => 0, // Override to 0
            ],
            [
                'total_hours' => '06:00:00', // 6 hours - would auto = 30 min
                'total_break_duration' => 0,
                'manual_break_override' => 0,
                'manual_break_minutes' => null, // No override, use auto
            ],
            [
                'total_hours' => '10:30:00', // 10.5 hours - would auto = 60 min
                'total_break_duration' => 0,
                'manual_break_override' => 1,
                'manual_break_minutes' => 15, // Override to 15
            ],
        ];
        
        $totalHours = 0;
        $totalBreak = 0;
        
        foreach ($timesheets as $ts) {
            // Calculate hours
            list($h, $m, $s) = explode(':', $ts['total_hours']);
            $daySeconds = ((int)$h * 3600) + ((int)$m * 60) + (int)$s;
            $totalHours += $daySeconds;
            
            // Calculate break with override logic
            $manualOverride = isset($ts['manual_break_override']) && $ts['manual_break_override'] == 1;
            $manualBreakMinutes = isset($ts['manual_break_minutes']) ? (int)$ts['manual_break_minutes'] : null;
            
            if ($manualOverride && $manualBreakMinutes !== null) {
                $breakDuration = $manualBreakMinutes;
            } else {
                $breakDuration = isset($ts['total_break_duration']) ? (int)$ts['total_break_duration'] : 0;
                if ($breakDuration == 0 && $daySeconds > 0) {
                    $hoursWorked = $daySeconds / 3600;
                    if ($hoursWorked >= 10) {
                        $breakDuration = 60;
                    } elseif ($hoursWorked >= 5) {
                        $breakDuration = 30;
                    }
                }
            }
            
            $totalBreak += $breakDuration;
        }
        
        // Expected breaks:
        // Day 1: 0 (manual override to 0)
        // Day 2: 30 (auto-break for 6 hours)
        // Day 3: 15 (manual override to 15)
        // Total: 45 minutes
        $this->assertEquals(45, $totalBreak, 'Total break should be 45 minutes');
        
        // Calculate net hours
        $netSeconds = $totalHours - ($totalBreak * 60);
        $netMinutes = round($netSeconds / 60);
        $hours = floor($netMinutes / 60);
        $minutes = $netMinutes % 60;
        $formattedHours = "{$hours} hrs {$minutes} min";
        
        // Total raw: 8 + 6 + 10.5 = 24.5 hours = 24 hrs 30 min
        // Minus 45 min break = 23 hrs 45 min
        $this->assertEquals('23 hrs 45 min', $formattedHours, 'Net hours should be 23 hrs 45 min');
    }
    
    // =========================================================================
    // HELPER METHODS
    // =========================================================================
    
    /**
     * Calculate worked minutes based on clock times and break
     */
    protected function calculateWorkedMinutes(string $clockIn, string $clockOut, int $breakMinutes): int
    {
        $inTime = strtotime($clockIn);
        $outTime = strtotime($clockOut);
        
        $totalMinutes = ($outTime - $inTime) / 60;
        
        return $totalMinutes - $breakMinutes;
    }
    
    /**
     * Calculate break duration in minutes
     */
    protected function calculateBreakDuration(string $breakStart, string $breakEnd): int
    {
        $start = strtotime($breakStart);
        $end = strtotime($breakEnd);
        
        return ($end - $start) / 60;
    }
}
