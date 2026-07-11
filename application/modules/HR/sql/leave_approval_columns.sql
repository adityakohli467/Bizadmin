-- Optional: adds approver/audit columns used by the manager leave approval flow.
-- Safe to run once. Without these columns approve/reject still work, but the
-- manager's comment (e.g. rejection reason) will not be persisted.

ALTER TABLE `HR_leave_management`
  ADD COLUMN `approver_id` INT NULL DEFAULT NULL AFTER `leave_status`,
  ADD COLUMN `approver_comment` TEXT NULL DEFAULT NULL AFTER `approver_id`,
  ADD COLUMN `approved_date` DATETIME NULL DEFAULT NULL AFTER `approver_comment`;
