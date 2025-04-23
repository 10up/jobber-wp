import { test, expect } from '@playwright/test';

test.describe('Plugin Installation and Activation', () => {
  test('can be installed and activated', async ({ page }) => {
    // Login to WordPress admin
    await page.goto('/wp-admin');
    await page.fill('#user_login', 'admin');
    await page.fill('#user_pass', 'password');
    await page.click('#wp-submit');

    // Navigate to plugins page
    await page.goto('/wp-admin/plugins.php');

    // Check if plugin is installed and activated
    const pluginRow = page.locator('tr[data-plugin="jobber/jobber.php"]');
    await expect(pluginRow).toBeVisible();
    
    // Check if plugin is activated
    const deactivateLink = pluginRow.locator('a.deactivate');
    await expect(deactivateLink).toBeVisible();
  });
});
