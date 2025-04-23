import { stopMockServer } from './mock-server';

export default async function globalTeardown() {
  // Stop the mock server
  await stopMockServer();
}
