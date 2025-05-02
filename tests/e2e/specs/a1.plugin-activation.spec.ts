import { test, expect } from '@playwright/test';

test.describe('Plugin Activation', () => {
  test('plugin can be activated', async ({ page }) => {
    // Navigate to plugins page.
    await page.goto('/wp-admin/plugins.php');

    // Find and click the activate link.
    const isActivated = await page.locator('tr[data-slug="jobber"].active').isVisible();
    if (!isActivated) {
      const activateLink = page.locator('tr[data-slug="jobber"] .activate a');
      await activateLink.click();
    }

    // Verify plugin is activated.
    await expect(page.locator('tr[data-slug="jobber"].active')).toBeVisible();
  });
});
