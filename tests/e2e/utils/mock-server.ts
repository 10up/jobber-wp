import { createServer, RequestListener } from 'http';
import { parse } from 'url';

// Mock responses for different endpoints
const mockResponses = {
  '/jobber/v1/get_form': {
    form: {
      iframeUrl: 'https://mock.jobber.com/forms/booking',
    },
  },
  '/jobber/v1/clients': {
    clients: [
      {
        id: 'mock-client-1',
        name: 'Test Client',
        email: 'test@example.com',
      },
    ],
  },
};

// Create request handler
const requestHandler: RequestListener = (req, res) => {
  const { pathname } = parse(req.url || '');
  
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  // Handle OPTIONS requests
  if (req.method === 'OPTIONS') {
    res.writeHead(200);
    res.end();
    return;
  }

  // Handle actual requests
  if (pathname && mockResponses[pathname]) {
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify(mockResponses[pathname]));
  } else {
    res.writeHead(404);
    res.end('Not Found');
  }
};

// Create and start the server
const server = createServer(requestHandler);
const port = process.env.MOCK_SERVER_PORT || 3000;

export const startMockServer = () => {
  return new Promise((resolve) => {
    server.listen(port, () => {
      console.log(`Mock server running at http://localhost:${port}`);
      resolve(server);
    });
  });
};

export const stopMockServer = () => {
  return new Promise((resolve) => {
    server.close(() => {
      console.log('Mock server stopped');
      resolve(null);
    });
  });
};
