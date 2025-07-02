/**
* minimal worker with the only goal to fulfil Chrome/Edge's PWA install requests
* no service worker caches are used, only the browser cache
* based upon 
* https://github.com/leopatras/simple_offline_pwa/blob/main/test_sw.js
*/
// console.log("sw loading");

self.addEventListener('activate', function(event) {
	// console.log("sw activate");
	event.waitUntil(self.clients.claim());
});

self.addEventListener('install', function(event) {
	// console.log("sw install");
	event.waitUntil(self.skipWaiting()); // Activate worker immediately
});

self.addEventListener('fetch', function(event) {
	// console.info('sw fetch url:' + event.request.url + ", mode:" + event.request.mode);
});