import { test, expect } from '@playwright/test';

test.describe('Block Insertion', () => {
  test('block exists and can be inserted', async ({ page }) => {
    // Navigate to post editor
    await page.goto('/wp-admin/post-new.php');

    // Wait for block editor to load
    await page.waitForSelector('.block-editor-writing-flow', { state: 'visible' });

    // Open block inserter
    await page.click('[aria-label="Add block"]');

    // Search for Jobber block
    await page.fill('[placeholder="Search"]', 'Jobber');

    // Check if block exists
    const block = page.locator('button:has-text("Jobber Forms")');
    await expect(block).toBeVisible();

    // Insert block
    await block.click();

    // Check if block is inserted
    await expect(page.locator('.wp-block-jobber-forms')).toBeVisible();
  });
});
