import { test, expect } from '@playwright/test';
import { BASE_URL } from '../utils/config';

test.describe('Basic WordPress Admin Tests', () => {
  test('should load WordPress admin dashboard', async ({ page }) => {
    await page.goto(`${BASE_URL}/wp-admin`);
    await expect(page).toHaveTitle(/Dashboard/);
  });

  test('should load Jobber plugin settings page', async ({ page }) => {
    await page.goto(`${BASE_URL}/wp-admin/admin.php?page=jobber`);
    await expect(page).toHaveTitle(/Jobber/);
  });
});
