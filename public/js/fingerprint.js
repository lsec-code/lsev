/**
 * Browser Fingerprinting Library
 * Generates unique device fingerprint using Canvas, WebGL, Audio, and other browser APIs
 */

class DeviceFingerprint {
    constructor() {
        this.components = {};
    }

    async generate() {
        await Promise.all([
            this.getCanvasFingerprint(),
            this.getWebGLFingerprint(),
            this.getAudioFingerprint(),
            this.getScreenFingerprint(),
            this.getNavigatorFingerprint(),
            this.getFontsFingerprint()
        ]);

        // Combine all components into a single hash
        const fingerprintString = JSON.stringify(this.components);
        return await this.hashString(fingerprintString);
    }

    async getCanvasFingerprint() {
        try {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = 200;
            canvas.height = 50;

            ctx.textBaseline = 'top';
            ctx.font = '14px Arial';
            ctx.textBaseline = 'alphabetic';
            ctx.fillStyle = '#f60';
            ctx.fillRect(125, 1, 62, 20);
            ctx.fillStyle = '#069';
            ctx.fillText('CloudHost 🔒', 2, 15);
            ctx.fillStyle = 'rgba(102, 204, 0, 0.7)';
            ctx.fillText('CloudHost 🔒', 4, 17);

            this.components.canvas = canvas.toDataURL();
        } catch (e) {
            this.components.canvas = 'unsupported';
        }
    }

    async getWebGLFingerprint() {
        try {
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');

            if (gl) {
                const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                this.components.webgl = {
                    vendor: gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL),
                    renderer: gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL),
                    version: gl.getParameter(gl.VERSION),
                    shadingLanguageVersion: gl.getParameter(gl.SHADING_LANGUAGE_VERSION)
                };
            } else {
                this.components.webgl = 'unsupported';
            }
        } catch (e) {
            this.components.webgl = 'unsupported';
        }
    }

    async getAudioFingerprint() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) {
                this.components.audio = 'unsupported';
                return;
            }

            const context = new AudioContext();
            const oscillator = context.createOscillator();
            const analyser = context.createAnalyser();
            const gainNode = context.createGain();
            const scriptProcessor = context.createScriptProcessor(4096, 1, 1);

            gainNode.gain.value = 0;
            oscillator.connect(analyser);
            analyser.connect(scriptProcessor);
            scriptProcessor.connect(gainNode);
            gainNode.connect(context.destination);

            scriptProcessor.onaudioprocess = (event) => {
                const output = event.outputBuffer.getChannelData(0);
                this.components.audio = Array.from(output.slice(0, 30)).reduce((a, b) => a + b, 0);
                scriptProcessor.disconnect();
                gainNode.disconnect();
                oscillator.disconnect();
                analyser.disconnect();
                context.close();
            };

            oscillator.start(0);
        } catch (e) {
            this.components.audio = 'unsupported';
        }
    }

    async getScreenFingerprint() {
        this.components.screen = {
            width: screen.width,
            height: screen.height,
            availWidth: screen.availWidth,
            availHeight: screen.availHeight,
            colorDepth: screen.colorDepth,
            pixelDepth: screen.pixelDepth,
            devicePixelRatio: window.devicePixelRatio || 1
        };
    }

    async getNavigatorFingerprint() {
        this.components.navigator = {
            userAgent: navigator.userAgent,
            language: navigator.language,
            languages: navigator.languages ? navigator.languages.join(',') : '',
            platform: navigator.platform,
            hardwareConcurrency: navigator.hardwareConcurrency || 'unknown',
            deviceMemory: navigator.deviceMemory || 'unknown',
            maxTouchPoints: navigator.maxTouchPoints || 0,
            vendor: navigator.vendor,
            cookieEnabled: navigator.cookieEnabled,
            doNotTrack: navigator.doNotTrack || 'unknown'
        };
    }

    async getFontsFingerprint() {
        const baseFonts = ['monospace', 'sans-serif', 'serif'];
        const testFonts = [
            'Arial', 'Verdana', 'Times New Roman', 'Courier New', 'Georgia',
            'Palatino', 'Garamond', 'Bookman', 'Comic Sans MS', 'Trebuchet MS',
            'Impact', 'Lucida Console'
        ];

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const text = 'mmmmmmmmmmlli';
        const detectedFonts = [];

        for (const font of testFonts) {
            let detected = false;
            for (const baseFont of baseFonts) {
                ctx.font = `72px ${baseFont}`;
                const baseWidth = ctx.measureText(text).width;

                ctx.font = `72px ${font}, ${baseFont}`;
                const testWidth = ctx.measureText(text).width;

                if (baseWidth !== testWidth) {
                    detected = true;
                    break;
                }
            }
            if (detected) {
                detectedFonts.push(font);
            }
        }

        this.components.fonts = detectedFonts.join(',');
    }

    async hashString(str) {
        const encoder = new TextEncoder();
        const data = encoder.encode(str);
        const hashBuffer = await crypto.subtle.digest('SHA-256', data);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    }
}

// Auto-generate and store fingerprint
(async function () {
    const fp = new DeviceFingerprint();
    const fingerprint = await fp.generate();

    // Store in sessionStorage
    sessionStorage.setItem('device_fingerprint', fingerprint);

    // Send to server via hidden input or AJAX
    const fpInputs = document.querySelectorAll('input[name="device_fingerprint"]');
    fpInputs.forEach(input => {
        input.value = fingerprint;
    });

    // Also add to all forms dynamically
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form.querySelector('input[name="device_fingerprint"]')) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'device_fingerprint';
            input.value = fingerprint;
            form.appendChild(input);
        }
    });
})();
