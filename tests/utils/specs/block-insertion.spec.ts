import { test, expect } from '@playwright/test';

test.describe('Block Insertion', () => {
  test.beforeEach(async ({ page }) => {
    // Login to WordPress admin
    await page.goto('/wp-admin');
    await page.fill('#user_login', 'admin');
    await page.fill('#user_pass', 'password');
    await page.click('#wp-submit');
  });

  test('block exists and can be inserted', async ({ page }) => {
    // Create a new post
    await page.goto('/wp-admin/post-new.php');

    // Wait for block editor to load
    await page.waitForSelector('.block-editor');

    // Click the block inserter
    await page.click('button[aria-label="Add block"]');

    // Search for Jobber Forms block
    await page.fill('input[placeholder="Search for a block"]', 'Jobber Forms');
    
    // Check if block exists in inserter
    const jobberBlock = page.locator('button:has-text("Jobber Forms")');
    await expect(jobberBlock).toBeVisible();

    // Insert the block
    await jobberBlock.click();

    // Check if block is inserted
    const insertedBlock = page.locator('.wp-block-jobber-forms');
    await expect(insertedBlock).toBeVisible();

    // Wait for the iframe to load
    const iframe = page.frameLocator('iframe[title="Jobber Form"]');
    await expect(iframe).toBeVisible();

    // Verify the iframe src matches our mock URL
    const iframeSrc = await page.locator('iframe[title="Jobber Form"]').getAttribute('src');
    expect(iframeSrc).toBe('https://mock.jobber.com/forms/booking');
  });
}); 