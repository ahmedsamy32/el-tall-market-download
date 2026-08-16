(() => {
    const form = document.querySelector('[data-diagnostics-form]');
    if (!form) {
        return;
    }

    const output = document.querySelector('[data-diagnostics-output]');
    const progressBar = form.querySelector('[data-progress-bar]');
    const progressValue = form.querySelector('[data-progress-value]');
    const submitButton = form.querySelector('[type="submit"]');

    const setOutput = (payload) => {
        if (!output) {
            return;
        }
        output.textContent = payload;
    };

    const setProgress = (percent) => {
        if (progressBar) {
            progressBar.style.width = `${percent}%`;
        }
        if (progressValue) {
            progressValue.textContent = `${percent}%`;
        }
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();
        const startedAt = Date.now();

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.dataset.originalText = submitButton.textContent || '';
            submitButton.textContent = 'Uploading...';
        }

        setProgress(0);
        setOutput('Running upload probe...');

        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.addEventListener('progress', (progressEvent) => {
            if (!progressEvent.lengthComputable) {
                return;
            }
            const percent = Math.round((progressEvent.loaded / progressEvent.total) * 100);
            setProgress(percent);
        });

        xhr.onreadystatechange = () => {
            if (xhr.readyState !== XMLHttpRequest.DONE) {
                return;
            }

            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = submitButton.dataset.originalText || 'Run';
            }

            const durationMs = Date.now() - startedAt;
            const responseText = (xhr.responseText || '').trim();

            let payload = null;
            try {
                payload = responseText ? JSON.parse(responseText) : null;
            } catch (error) {
                payload = null;
            }

            const result = {
                client: {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    duration_ms: durationMs,
                },
                response: payload || {
                    raw_preview: responseText.slice(0, 400) || '(empty response)',
                },
            };

            setOutput(JSON.stringify(result, null, 2));
        };

        xhr.onerror = () => {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = submitButton.dataset.originalText || 'Run';
            }

            const durationMs = Date.now() - startedAt;
            setOutput(JSON.stringify({
                client: {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    duration_ms: durationMs,
                },
                response: {
                    error: 'Network error while uploading.',
                },
            }, null, 2));
        };

        xhr.onabort = () => {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = submitButton.dataset.originalText || 'Run';
            }

            const durationMs = Date.now() - startedAt;
            setOutput(JSON.stringify({
                client: {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    duration_ms: durationMs,
                },
                response: {
                    error: 'Upload aborted by the browser.',
                },
            }, null, 2));
        };

        xhr.send(formData);
    });
})();
