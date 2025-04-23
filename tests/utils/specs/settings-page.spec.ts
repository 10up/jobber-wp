import { test, expect } from '@playwright/test';

test.describe('Settings Page', () => {
  test.beforeEach(async ({ page }) => {
    // Login to WordPress admin
    await page.goto('/wp-admin');
    await page.fill('#user_login', 'admin');
    await page.fill('#user_pass', 'password');
    await page.click('#wp-submit');
  });

  test('settings page exists and contains connect button', async ({ page }) => {
    // Navigate to settings page
    await page.goto('/wp-admin/options-general.php?page=jobber_settings');

    // Check if settings page exists
    await expect(page.locator('h1:has-text("Jobber Forms")')).toBeVisible();

    // Check if connect button exists
    const connectButton = page.locator('a:has-text("Connect to Jobber")');
    await expect(connectButton).toBeVisible();
  });
}); 