import { startMockServer } from './mock-server';

export default async function globalSetup() {
  // Start the mock server
  await startMockServer();
} 