(() => {
    const root = document.documentElement;
    const storageKey = 'souk_altal_theme';

    const applyTheme = (theme) => {
        if (theme === 'dark') {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
        localStorage.setItem(storageKey, theme);
        document.querySelectorAll('.theme-toggle-icon').forEach((el) => {
            el.textContent = theme === 'dark' ? '☀' : '☾';
        });
    };

    const storedTheme = localStorage.getItem(storageKey);
    const preferredTheme = storedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    applyTheme(preferredTheme);

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-theme-toggle]');
        if (!button) {
            return;
        }
        applyTheme(root.classList.contains('dark') ? 'light' : 'dark');
    });

    const menuOverlay = document.querySelector('[data-admin-menu-overlay]');
    const menuToggleButtons = document.querySelectorAll('[data-admin-menu-toggle]');
    const menuCloseButtons = document.querySelectorAll('[data-admin-menu-close]');
    const sidebarNav = document.querySelector('.admin-sidebar-nav');

    const layoutShell = document.querySelector('.admin-layout-shell');
    const openMenu = () => {
        root.classList.add('admin-menu-open');
        document.body.classList.add('admin-menu-open');
        if (layoutShell) {
            layoutShell.classList.add('admin-menu-open');
        }
    };
    const closeMenu = () => {
        root.classList.remove('admin-menu-open');
        document.body.classList.remove('admin-menu-open');
        if (layoutShell) {
            layoutShell.classList.remove('admin-menu-open');
        }
    };
    const toggleMenu = () => {
        if (root.classList.contains('admin-menu-open')) {
            closeMenu();
            return;
        }
        openMenu();
    };

    menuToggleButtons.forEach((button) => {
        button.addEventListener('click', toggleMenu);
    });

    menuCloseButtons.forEach((button) => {
        button.addEventListener('click', closeMenu);
    });

    if (menuOverlay) {
        menuOverlay.addEventListener('click', closeMenu);
    }

    if (sidebarNav) {
        sidebarNav.addEventListener('click', (event) => {
            const link = event.target.closest('a');
            if (!link) {
                return;
            }
            if (window.innerWidth < 1024) {
                closeMenu();
            }
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });

    const toastWrap = document.createElement('div');
    toastWrap.className = 'toast-wrap';
    document.body.appendChild(toastWrap);

    let isUploading = false;
    let activeUploadForm = null;

    window.addEventListener('beforeunload', (event) => {
        if (!isUploading) {
            return;
        }
        event.preventDefault();
        event.returnValue = '';
    });

    const setUploading = (state, form = null) => {
        isUploading = state;
        activeUploadForm = state ? form : null;
    };

    const closeToast = (toast) => {
        if (!toast) {
            return;
        }
        toast.classList.add('toast-hide');
        window.setTimeout(() => toast.remove(), 260);
    };

    const showToast = (message, type = 'info') => {
        if (!message) {
            return;
        }
        const toast = document.createElement('div');
        toast.className = `toast-item toast-${type}`;
        const messageNode = document.createElement('span');
        messageNode.className = 'toast-message';
        messageNode.textContent = message;

        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'toast-close';
        closeButton.setAttribute('aria-label', 'إغلاق');
        closeButton.textContent = 'X';
        closeButton.addEventListener('click', () => closeToast(toast));

        toast.appendChild(messageNode);
        toast.appendChild(closeButton);
        toastWrap.appendChild(toast);
    };

    document.querySelectorAll('[data-flash-message]').forEach((node) => {
        showToast(node.getAttribute('data-flash-message'), node.getAttribute('data-flash-type') || 'info');
    });

    const CHUNK_SIZE = 5 * 1024 * 1024;
    const CHUNK_THRESHOLD = 8 * 1024 * 1024;

    const getFieldValue = (form, name) => {
        const field = form.querySelector(`[name="${name}"]`);
        if (!field) {
            return '';
        }
        if (field.type === 'checkbox') {
            return field.checked ? (field.value || '1') : '';
        }
        return field.value || '';
    };

    const setProgress = (progressBar, progressValue, percent) => {
        const safePercent = Math.max(0, Math.min(100, percent));
        if (progressBar) {
            progressBar.style.width = `${safePercent}%`;
        }
        if (progressValue) {
            progressValue.textContent = `${safePercent}%`;
        }
    };

    const sendChunk = (endpoint, form, file, uploadId, chunkIndex, totalChunks, progressBar, progressValue) => new Promise((resolve, reject) => {
        const start = chunkIndex * CHUNK_SIZE;
        const end = Math.min(file.size, start + CHUNK_SIZE);
        const blob = file.slice(start, end);
        const formData = new FormData();

        formData.append('action', 'chunk');
        formData.append('upload_id', uploadId);
        formData.append('chunk_index', String(chunkIndex));
        formData.append('total_chunks', String(totalChunks));
        formData.append('chunk', blob, file.name);

        const csrf = getFieldValue(form, 'csrf_token');
        if (csrf) {
            formData.append('csrf_token', csrf);
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', endpoint, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.addEventListener('progress', (progressEvent) => {
            if (!progressEvent.lengthComputable) {
                return;
            }
            const loaded = start + progressEvent.loaded;
            const percent = Math.round((loaded / file.size) * 100);
            setProgress(progressBar, progressValue, percent);
        });

        xhr.onreadystatechange = () => {
            if (xhr.readyState !== XMLHttpRequest.DONE) {
                return;
            }

            if (xhr.status === 0) {
                reject('تم قطع الاتصال أثناء الرفع. راجع حدود الاستضافة أو اتصال الشبكة.');
                return;
            }

            let payload = null;
            try {
                payload = JSON.parse(xhr.responseText);
            } catch (error) {
                const preview = (xhr.responseText || '').trim().slice(0, 180);
                reject(preview ? `حدث رد غير متوقع من السيرفر: ${preview}` : 'السيرفر لم يرجع ردًا صالحًا أثناء الرفع.');
                return;
            }

            if (xhr.status >= 200 && xhr.status < 300 && payload && payload.success) {
                resolve(payload);
                return;
            }

            reject((payload && payload.message) || 'فشلت العملية.');
        };

        xhr.onerror = () => reject('حدث خطأ في الاتصال أثناء الرفع.');
        xhr.onabort = () => reject('تم إلغاء الرفع قبل اكتماله.');
        xhr.send(formData);
    });

    const sendComplete = (endpoint, form, file, uploadId, totalChunks) => new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('action', 'complete');
        formData.append('upload_id', uploadId);
        formData.append('total_chunks', String(totalChunks));
        formData.append('file_name', file.name);
        formData.append('file_size', String(file.size));
        formData.append('file_type', file.type || '');
        formData.append('version', getFieldValue(form, 'version'));
        formData.append('update_notes', getFieldValue(form, 'update_notes'));
        formData.append('platform', getFieldValue(form, 'platform'));
        formData.append('is_latest', getFieldValue(form, 'is_latest'));
        formData.append('id', getFieldValue(form, 'id'));

        const csrf = getFieldValue(form, 'csrf_token');
        if (csrf) {
            formData.append('csrf_token', csrf);
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', endpoint, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onreadystatechange = () => {
            if (xhr.readyState !== XMLHttpRequest.DONE) {
                return;
            }

            if (xhr.status === 0) {
                reject('تم قطع الاتصال أثناء الرفع. راجع حدود الاستضافة أو اتصال الشبكة.');
                return;
            }

            let payload = null;
            try {
                payload = JSON.parse(xhr.responseText);
            } catch (error) {
                const preview = (xhr.responseText || '').trim().slice(0, 180);
                reject(preview ? `حدث رد غير متوقع من السيرفر: ${preview}` : 'السيرفر لم يرجع ردًا صالحًا أثناء الرفع.');
                return;
            }

            if (xhr.status >= 200 && xhr.status < 300 && payload && payload.success) {
                resolve(payload);
                return;
            }

            reject((payload && payload.message) || 'فشلت العملية.');
        };

        xhr.onerror = () => reject('حدث خطأ في الاتصال أثناء الرفع.');
        xhr.onabort = () => reject('تم إلغاء الرفع قبل اكتماله.');
        xhr.send(formData);
    });

    const uploadChunked = async (form, file, progressBar, progressValue, submitButton) => {
        const endpoint = form.getAttribute('data-chunk-endpoint');
        if (!endpoint) {
            showToast('لم يتم ضبط مسار رفع الأجزاء في الصفحة.', 'error');
            return;
        }

        const uploadId = `${Date.now().toString(36)}${Math.random().toString(36).slice(2, 8)}`;
        const totalChunks = Math.ceil(file.size / CHUNK_SIZE);

        setUploading(true, form);
        setProgress(progressBar, progressValue, 0);

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.dataset.originalText = submitButton.textContent;
            submitButton.textContent = 'جاري الرفع...';
        }

        try {
            for (let index = 0; index < totalChunks; index += 1) {
                await sendChunk(endpoint, form, file, uploadId, index, totalChunks, progressBar, progressValue);
            }

            setProgress(progressBar, progressValue, 100);
            const payload = await sendComplete(endpoint, form, file, uploadId, totalChunks);

            showToast(payload.message || 'تمت العملية بنجاح.', 'success');
            if (payload.redirect) {
                window.location.href = payload.redirect;
            } else {
                window.location.reload();
            }
        } catch (error) {
            showToast(typeof error === 'string' ? error : 'فشلت العملية.', 'error');
        } finally {
            setUploading(false);
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = submitButton.dataset.originalText || 'حفظ';
            }
        }
    };

    document.querySelectorAll('[data-xhr-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const progressBar = form.querySelector('[data-progress-bar]');
            const progressValue = form.querySelector('[data-progress-value]');
            const submitButton = form.querySelector('[type="submit"]');
            const action = form.getAttribute('action');
            const formData = new FormData(form);
            const xhr = new XMLHttpRequest();
            const fileInput = form.querySelector('input[type="file"][name="app_file"]');
            const file = fileInput && fileInput.files && fileInput.files.length > 0 ? fileInput.files[0] : null;
            const useChunked = Boolean(file && form.hasAttribute('data-chunk-endpoint') && file.size >= CHUNK_THRESHOLD);

            if (useChunked) {
                uploadChunked(form, file, progressBar, progressValue, submitButton);
                return;
            }

            setUploading(true, form);

            if (progressBar) {
                progressBar.style.width = '0%';
            }
            if (progressValue) {
                progressValue.textContent = '0%';
            }
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.dataset.originalText = submitButton.textContent;
                submitButton.textContent = 'جاري الرفع...';
            }

            xhr.open('POST', action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.addEventListener('progress', (progressEvent) => {
                if (!progressEvent.lengthComputable) {
                    return;
                }
                const percent = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                if (progressBar) {
                    progressBar.style.width = `${percent}%`;
                }
                if (progressValue) {
                    progressValue.textContent = `${percent}%`;
                }
            });

            xhr.onreadystatechange = () => {
                if (xhr.readyState !== XMLHttpRequest.DONE) {
                    return;
                }

                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = submitButton.dataset.originalText || 'حفظ';
                }

                setUploading(false);

                if (xhr.status === 413) {
                    showToast('السيرفر رفض الملف لأنه أكبر من الحد المسموح أو يوجد حد آخر على مستوى الاستضافة.', 'error');
                    return;
                }

                if (xhr.status === 0) {
                    showToast('تم قطع الاتصال أثناء الرفع. راجع حدود الاستضافة أو اتصال الشبكة.', 'error');
                    return;
                }

                let payload = null;
                try {
                    payload = JSON.parse(xhr.responseText);
                } catch (error) {
                    const preview = (xhr.responseText || '').trim().slice(0, 180);
                    showToast(preview ? `حدث رد غير متوقع من السيرفر: ${preview}` : 'السيرفر لم يرجع ردًا صالحًا أثناء الرفع.', 'error');
                    return;
                }

                if (xhr.status >= 200 && xhr.status < 300 && payload && payload.success) {
                    showToast(payload.message || 'تمت العملية بنجاح.', 'success');
                    if (payload.redirect) {
                        window.location.href = payload.redirect;
                    } else {
                        window.location.reload();
                    }
                    return;
                }

                showToast((payload && payload.message) || 'فشلت العملية.', 'error');
            };

            xhr.onerror = () => {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = submitButton.dataset.originalText || 'حفظ';
                }

                setUploading(false);
                showToast('حدث خطأ في الاتصال أثناء الرفع.', 'error');
            };

            xhr.onabort = () => {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = submitButton.dataset.originalText || 'حفظ';
                }

                setUploading(false);
                showToast('تم إلغاء الرفع قبل اكتماله.', 'error');
            };

            xhr.send(formData);
        });
    });

    const blockNavigation = (event) => {
        if (!isUploading) {
            return;
        }
        if (event.target.closest('.toast-close') || event.target.closest('.toast-item')) {
            return;
        }
        if (event.target.closest('[data-admin-menu-toggle]') || event.target.closest('[data-admin-menu-close]')) {
            return;
        }
        if (activeUploadForm && activeUploadForm.contains(event.target)) {
            return;
        }
        const link = event.target.closest('a');
        const button = event.target.closest('button');
        if (!link && !button) {
            return;
        }
        if (link && link.hasAttribute('data-allow-navigation')) {
            return;
        }
        event.preventDefault();
        event.stopImmediatePropagation();
        showToast('يتم رفع الملف الآن، الرجاء الانتظار حتى ينتهي الرفع.', 'info');
    };

    document.addEventListener('click', blockNavigation, true);
    document.addEventListener('submit', (event) => {
        if (!isUploading) {
            return;
        }
        if (activeUploadForm && event.target === activeUploadForm) {
            return;
        }
        event.preventDefault();
        event.stopImmediatePropagation();
        showToast('يتم رفع الملف الآن، الرجاء الانتظار حتى ينتهي الرفع.', 'info');
    }, true);

    document.querySelectorAll('[data-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            const text = button.getAttribute('data-copy') || '';
            try {
                await navigator.clipboard.writeText(text);
                showToast('تم النسخ بنجاح.', 'success');
            } catch (error) {
                showToast('تعذر النسخ.', 'error');
            }
        });
    });
})();
