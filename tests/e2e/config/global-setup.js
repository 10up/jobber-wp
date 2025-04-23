// @ts-check
import { chromium } from '@playwright/test';

async function globalSetup() {
  const browser = await chromium.launch();
  const page = await browser.newPage();

  // Login and save the authentication state
  await page.goto('http://localhost:8888/wp-admin');
  await page.fill('#user_login', 'admin');
  await page.fill('#user_pass', 'password');
  await page.click('#wp-submit');

  // Save the authentication state
  await page.context().storageState({ path: 'state.json' });
  await browser.close();
}

export default globalSetup;
