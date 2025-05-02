import { test, expect } from '@playwright/test';
import { login, addJobberBlock } from '../utils/auth';
import { WP_ADMIN_USER, WP_ADMIN_PASSWORD, BASE_URL } from '../utils/config';

test.describe('Block Insertion', () => {
  test.beforeEach(async ({ page }) => {
    await login(page, WP_ADMIN_USER.username, WP_ADMIN_PASSWORD);
  });

  test('block exists and can be inserted', async ({ page }) => {
    await page.goto( `/wp-admin/post-new.php` );
    await page.screenshot({ path: 'debug-1-editor-loaded.png', fullPage: true });
    
    // Close Gutenberg the welcome panel if it is visible.
    await page.waitForTimeout(3000);
    const gutenbergEditorWelcomePanelLocator = await page.getByLabel( 'Welcome to the editor' );
    await page.screenshot({ path: 'debug-2-after-welcome-panel.png', fullPage: true });
    
    if ( await gutenbergEditorWelcomePanelLocator.isVisible() ) {
      await gutenbergEditorWelcomePanelLocator.getByLabel( 'Close', { exact: true } ).click();
    }

    // Insert booking calender block.
    await page.locator("button.editor-document-tools__inserter-toggle").click();
    await page.screenshot({ path: 'debug-3-after-inserter-toggle.png', fullPage: true });
    await page.locator("input.components-input-control__input").fill( 'Jobber' );
    await page.screenshot({ path: 'debug-4-after-search-jobber.png', fullPage: true });
    await page.getByRole( 'option', { name: 'Jobber' } ).click();

    // await page.waitForTimeout(1000); // Wait for block to render
    let found = false;
    for (const frame of page.frames()) {
      const count = await frame.locator('.wp-block-jobber-forms').count();
      console.log('Frame:', frame.url(), 'Jobber block count:', count);
      if (count > 0) {
        found = true;
        break;
      }
    }
    expect(found).toBeTruthy();
  });
});
