import { request } from '@playwright/test';
import { WP_ADMIN_USER, WP_ADMIN_PASSWORD } from './config';

async function globalSetup() {
  const requestContext = await request.newContext();
  
  // Login to WordPress admin
  await requestContext.post('/wp-login.php', {
    form: {
      log: WP_ADMIN_USER.username,
      pwd: WP_ADMIN_PASSWORD,
      wp-submit: 'Log In',
      redirect_to: '/wp-admin/',
      testcookie: '1'
    }
  });

  // Verify login was successful
  const response = await requestContext.get('/wp-admin/');
  if (!response.ok()) {
    throw new Error('Failed to login to WordPress admin');
  }

  // Store authentication state
  await requestContext.storageState({ path: 'storageState.json' });
  await requestContext.dispose();
}

export default globalSetup;
