import { test, expect } from '@playwright/test';
import { login } from '../utils/auth';
import { WP_ADMIN_USER, WP_ADMIN_PASSWORD, BASE_URL } from '../utils/config';

test.describe('Settings Page', () => {
  test.beforeEach(async ({ page }) => {
    await login(page, WP_ADMIN_USER.username, WP_ADMIN_PASSWORD);
    await page.goto(`${BASE_URL}/wp-admin/options-general.php?page=jobber_settings`);
  });

  test('settings page contains required elements', async ({ page }) => {
    // Check for logo
    await expect(page.locator('.jobber-settings__logo img')).toBeVisible();
    
    // Check for instructions.
    await expect(page.locator('p:has-text("The Jobber plugin allows you to easily embed your Booking and Request forms")')).toBeVisible();
    
    // Check for connect button.
    await expect(page.locator('a:has-text("Connect")')).toBeVisible();    
  });

  test('connect button links to auth endpoint', async ({ page }) => {
    // Check for connect button.
    const connectButton = page.locator('a:has-text("Connect")');
    await connectButton.click();

    // Check for auth endpoint.
    expect(page.url()).toContain('auth?clientUrl=');
  });
});
