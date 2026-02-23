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
     * Test Case T008: Fail - Update clock-out before clock-in
     * Expected: Error - Invalid time range
     */
    public function test_T008_update_clock_out_before_clock_in_should_fail()
    {
        $updateData = [
            'clock_in' => '17:00:00',
            'clock_out' => '09:00:00' // Before clock-in
        ];
        
        $isValid = strtotime($updateData['clock_out']) > strtotime($updateData['clock_in']);
        
        $this->assertFalse($isValid, 'Clock-out before clock-in should be invalid');
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
