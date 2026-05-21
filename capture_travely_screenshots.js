const fs = require('fs');
const path = require('path');
const { spawn } = require('child_process');

const chromePath = process.env.CHROME_PATH || 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const baseUrl = process.env.TRAVELY_BASE_URL || 'http://127.0.0.1:8090';
const outDir = process.env.TRAVELY_SCREENSHOT_DIR || 'C:\\VuxNgoc\\travely_screenshots';
const remotePort = Number(process.env.TRAVELY_CHROME_PORT || 9224);
const profileDir = path.join(outDir, '.chrome-profile');

fs.mkdirSync(outDir, { recursive: true });
fs.rmSync(profileDir, { recursive: true, force: true });
fs.mkdirSync(profileDir, { recursive: true });

function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function fetchJson(url, options) {
  const response = await fetch(url, options);
  if (!response.ok) {
    throw new Error(`${response.status} ${response.statusText}: ${url}`);
  }
  return response.json();
}

async function waitForChrome() {
  const endpoint = `http://127.0.0.1:${remotePort}/json/version`;
  for (let i = 0; i < 80; i += 1) {
    try {
      return await fetchJson(endpoint);
    } catch (_) {
      await sleep(250);
    }
  }
  throw new Error('Chrome DevTools endpoint did not start.');
}

function createCdpClient(wsUrl) {
  const ws = new WebSocket(wsUrl);
  let nextId = 1;
  const pending = new Map();
  const events = new Map();

  ws.onmessage = (message) => {
    const payload = JSON.parse(message.data);
    if (payload.id && pending.has(payload.id)) {
      const { resolve, reject } = pending.get(payload.id);
      pending.delete(payload.id);
      if (payload.error) {
        reject(new Error(payload.error.message || JSON.stringify(payload.error)));
      } else {
        resolve(payload.result || {});
      }
      return;
    }

    if (payload.method && events.has(payload.method)) {
      for (const listener of events.get(payload.method)) {
        listener(payload.params || {});
      }
    }
  };

  const ready = new Promise((resolve, reject) => {
    ws.onopen = resolve;
    ws.onerror = reject;
  });

  return {
    ready,
    send(method, params = {}) {
      const id = nextId;
      nextId += 1;
      ws.send(JSON.stringify({ id, method, params }));
      return new Promise((resolve, reject) => {
        pending.set(id, { resolve, reject });
      });
    },
    once(method) {
      return new Promise((resolve) => {
        const listener = (params) => {
          const list = events.get(method) || [];
          events.set(method, list.filter((item) => item !== listener));
          resolve(params);
        };
        const list = events.get(method) || [];
        list.push(listener);
        events.set(method, list);
      });
    },
    close() {
      ws.close();
    },
  };
}

async function navigate(client, url) {
  const loaded = client.once('Page.loadEventFired');
  await client.send('Page.navigate', { url });
  await loaded;
  await sleep(1200);
}

async function capture(client, fileName, url) {
  await navigate(client, url);
  const result = await client.send('Page.captureScreenshot', {
    format: 'png',
    captureBeyondViewport: true,
    fromSurface: true,
  });
  fs.writeFileSync(path.join(outDir, fileName), Buffer.from(result.data, 'base64'));
  console.log(`${fileName} <= ${url}`);
}

async function main() {
  const chrome = spawn(chromePath, [
    '--headless=new',
    '--disable-gpu',
    '--no-first-run',
    '--no-default-browser-check',
    '--hide-scrollbars',
    `--remote-debugging-port=${remotePort}`,
    `--user-data-dir=${profileDir}`,
    'about:blank',
  ], { stdio: 'ignore' });

  try {
    await waitForChrome();
    const pages = await fetchJson(`http://127.0.0.1:${remotePort}/json/list`);
    const page = pages.find((item) => item.type === 'page');
    if (!page) {
      throw new Error('No Chrome page target found.');
    }

    const client = createCdpClient(page.webSocketDebuggerUrl);
    await client.ready;
    await client.send('Page.enable');
    await client.send('Runtime.enable');
    await client.send('Emulation.setDeviceMetricsOverride', {
      width: 1440,
      height: 1100,
      deviceScaleFactor: 1,
      mobile: false,
    });

    const publicPages = [
      ['01_trang_chu.png', `${baseUrl}/`],
      ['02_danh_sach_tour.png', `${baseUrl}/tours`],
      ['03_chi_tiet_tour.png', `${baseUrl}/tour/sapa-san-may-va-ruong-bac-thang-3n2d`],
      ['04_tour_nuoc_ngoai.png', `${baseUrl}/tours/foreign`],
      ['05_lien_he.png', `${baseUrl}/contact`],
      ['06_dang_nhap.png', `${baseUrl}/login`],
      ['07_dang_ky.png', `${baseUrl}/register`],
    ];

    for (const [fileName, url] of publicPages) {
      await capture(client, fileName, url);
    }

    await navigate(client, `${baseUrl}/login`);
    await client.send('Runtime.evaluate', {
      expression: `
        document.querySelector('input[name="email"]').value = 'admin';
        document.querySelector('input[name="password"]').value = '123456';
        document.querySelector('form').requestSubmit();
      `,
      awaitPromise: true,
    });
    await sleep(2500);

    const adminPages = [
      ['08_admin_dashboard.png', `${baseUrl}/admin`],
      ['09_admin_tours.png', `${baseUrl}/admin/tours`],
      ['10_admin_bookings.png', `${baseUrl}/admin/bookings`],
    ];

    for (const [fileName, url] of adminPages) {
      await capture(client, fileName, url);
    }

    client.close();
  } finally {
    chrome.kill();
  }
}

main().catch((error) => {
  console.error(error.stack || error.message);
  process.exit(1);
});
