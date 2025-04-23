import { Page } from '@playwright/test';
import { BASE_URL } from './config';

/**
 * Ensures the user is logged in to WordPress admin
 * @param page Playwright page object
 */
export async function ensureLoggedIn(page: Page): Promise<void> {
  await page.goto('http://localhost:8888/wp-admin');
  
  // Check if already logged in by looking for the admin bar
  const isLoggedIn = await page.locator('#wp-admin-bar-my-account').isVisible();
  
  if (!isLoggedIn) {
    // Login to WordPress admin
    await page.fill('#user_login', 'admin');
    await page.fill('#user_pass', 'password');
    await page.click('#wp-submit');
    
    // Wait for login to complete
    await page.waitForLoadState('networkidle');
  }
}

export async function login(page: Page, username: string, password: string) {
  await page.goto(`${BASE_URL}/wp-login.php`);
  await page.fill('#user_login', username);
  await page.fill('#user_pass', password);
  await page.click('#wp-submit');
  await page.waitForURL('**/wp-admin/**');
}
