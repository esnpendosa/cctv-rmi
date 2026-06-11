import { readFileSync, existsSync } from 'node:fs';

let webserver_port = 8000;
try {
	const configUrl = new URL('../../../sites/common_site_config.json', import.meta.url);
	if (existsSync(configUrl)) {
		const common_site_config = JSON.parse(readFileSync(configUrl, 'utf8')) as { webserver_port: string | number };
		if (common_site_config && common_site_config.webserver_port) {
			webserver_port = Number(common_site_config.webserver_port);
		}
	}
} catch (e) {
	// Ignore errors and keep default port
}

export default {
	'^/(app|api|assets|files|private)': {
		target: `http://127.0.0.1:${webserver_port}`,
		ws: true,
		router: function (req) {
			const site_name = req.headers?.host?.split(':')[0];
			return `http://${site_name ?? 'localhost'}:${webserver_port}`;
		}
	}
};
