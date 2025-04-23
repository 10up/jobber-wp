import { defineConfig, devices } from '@playwright/test';
import { startMockServer, stopMockServer } from './utils/mock-server';

export default defineConfig({
  testDir: './tests/e2e/specs',
  /* Maximum time one test can run for. */
  timeout: 60 * 1000,
  expect: {
    /**
     * Maximum time expect() should wait for the condition to be met.
     * For example in `await expect(locator).toHaveText();`
     */
    timeout: 20000
  },
  /* Folder for test artifacts such as screenshots, videos, traces, etc. */
  outputDir: './test-results/report',
  /* Run tests in files in parallel */
  fullyParallel: false,
  /* Fail the build on CI if you accidentally left test.only in the source code. */
  forbidOnly: !!process.env.CI,
  /* Retry on CI only */
  retries: process.env.CI ? 2 : 0,
  /* Opt out of parallel tests on CI. */
  workers: 1,
  /* Reporter to use. See https://playwright.dev/docs/test-reporters */
  reporter: [
    ["list"],
    ["html", { outputFolder: './test-results/playwright-report' }],
  ],
  /* Shared settings for all the projects below. See https://playwright.dev/docs/api/class-testoptions. */
  use: {
    /* Base URL to use in actions like `await page.goto('/')`. */
    baseURL: process.env.WP_BASE_URL || 'http://localhost:8889',
    /* Collect trace when retrying the failed test. See https://playwright.dev/docs/trace-viewer */
    trace: 'retain-on-failure',
    // Capture screenshot after each test failure.
    screenshot: 'only-on-failure',
    // Record video only when retrying a test for the first time.
    video: 'on-first-retry',
    headless: true,
    // Viewport used for all pages in the context.
    viewport: { width: 1280, height: 720 },
  },
  /* Configure projects for major browsers */
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
  webServer: [
    {
      command: 'npm run wp-env start',
      url: 'http://localhost:8889',
      reuseExistingServer: !process.env.CI,
    },
    {
      command: 'node tests/e2e/utils/mock-server.js',
      url: 'http://localhost:3000',
      reuseExistingServer: !process.env.CI,
    },
  ],
  globalSetup: './tests/e2e/utils/global-setup.ts',
  globalTeardown: './tests/e2e/utils/global-teardown.ts',
});
