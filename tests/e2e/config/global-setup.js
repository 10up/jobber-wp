import { chromium } from '@playwright/test';

module.exports = async ( config ) => {
  const { baseURL } = config.projects[ 0 ].use;
	const browser = await chromium.launch();
	const context = await browser.newContext();
	const page = await context.newPage();

  // Login and save the authentication state
  await page.goto( `${ baseURL }/wp-admin` );
  await page.fill( '#user_login', 'admin' );
  await page.fill( '#user_pass', 'password' );
  await page.click( '#wp-submit' );

  // Save the authentication state
  await page.context().storageState( { path: 'state.json' } );
  await browser.close();
};
