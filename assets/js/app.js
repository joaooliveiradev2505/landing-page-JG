/**
 * Landing Page Híbrida — João Gabriel Santana de Oliveira
 * Alternância instantânea entre Visão Empresas (B2B Local) e Visão Recrutadores,
 * Orçamento Rápido integrado ao WhatsApp e Gerador de QR Code em alta resolução.
 */
document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const waNumber = body.dataset.waNumber || '5511988555937';

    const modeButtons = document.querySelectorAll('[data-switch-mode]');
    const viewBusiness = document.getElementById('view-business');
    const viewRecruiter = document.getElementById('view-recruiter');
    const footerBusiness = document.querySelector('.footer-contacts-business');
    const footerRecruiter = document.querySelector('.footer-contacts-recruiter');
    const floatingWaBtn = document.getElementById('floatingWaBtn');

    function setMode(mode, pushUrl = true) {
        const targetMode = mode === 'recruiter' ? 'recruiter' : 'business';
        body.dataset.activeMode = targetMode;

        modeButtons.forEach((btn) => {
            btn.classList.toggle('active', btn.dataset.switchMode === targetMode);
        });

        if (targetMode === 'business') {
            viewBusiness.classList.remove('d-none');
            viewRecruiter.classList.add('d-none');
            footerBusiness?.classList.replace('d-none', 'd-flex');
            footerRecruiter?.classList.replace('d-flex', 'd-none');
            if (floatingWaBtn) {
                floatingWaBtn.href = `https://wa.me/${waNumber}?text=${encodeURIComponent('Olá João! Vi sua página de serviços e gostaria de solicitar um orçamento.')}`;
            }
        } else {
            viewRecruiter.classList.remove('d-none');
            viewBusiness.classList.add('d-none');
            footerRecruiter?.classList.replace('d-none', 'd-flex');
            footerBusiness?.classList.replace('d-flex', 'd-none');
            if (floatingWaBtn) {
                floatingWaBtn.href = `https://wa.me/${waNumber}?text=${encodeURIComponent('Olá João Gabriel! Vi seu portfólio técnico e gostaria de conversar sobre uma oportunidade Full-Stack.')}`;
            }
        }

        if (pushUrl && window.history.replaceState) {
            const url = new URL(window.location.href);
            url.searchParams.set('mode', targetMode);
            window.history.replaceState({}, '', url.toString());
        }
    }

    modeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            setMode(btn.dataset.switchMode, true);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // Formulário de Pré-Orçamento Rápido (30s) -> Envia para endpoint PHP e abre WhatsApp
    const quickQuoteForm = document.getElementById('quickQuoteForm');
    if (quickQuoteForm) {
        quickQuoteForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = document.getElementById('qName')?.value.trim() || '';
            const company = document.getElementById('qCompany')?.value.trim() || '';
            const service = document.getElementById('qService')?.value || 'Solução sob medida';
            const details = document.getElementById('qDetails')?.value.trim() || '';
            const urlParams = new URLSearchParams(window.location.search);

            try {
                const response = await fetch('?action=quick_quote', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        name,
                        company,
                        service,
                        details,
                        utm_source: urlParams.get('utm_source') || 'lp_form',
                    }),
                });
                const result = await response.json();
                if (result && result.whatsapp_url) {
                    window.open(result.whatsapp_url, '_blank', 'noopener');
                    return;
                }
            } catch (err) {
                // Fallback client-side caso esteja rodando estático no GitHub Pages
            }

            const fallbackMsg = [
                'Olá João! Tudo bem?',
                'Vi sua página e gostaria de solicitar um orçamento rápido:',
                '',
                `• *Nome:* ${name || 'Não informado'}`,
                company ? `• *Empresa/Ramo:* ${company}` : null,
                `• *Solução:* ${service}`,
                details ? `• *Resumo:* ${details}` : null,
            ].filter(Boolean).join('\n');

            window.open(`https://wa.me/${waNumber}?text=${encodeURIComponent(fallbackMsg)}`, '_blank', 'noopener');
        });
    }

    // =========================================================================
    // Gerador de QR Code Dinâmico (Empresas / Direto WhatsApp / Recrutador)
    // =========================================================================
    const qrContainer = document.getElementById('qrCanvasContainer');
    const qrCustomUrlInput = document.getElementById('qrCustomUrl');
    const qrTargetButtons = document.querySelectorAll('[data-qr-target]');
    const qrCardSubtitle = document.getElementById('qrCardSubtitle');
    const copyQrLinkBtn = document.getElementById('copyQrLinkBtn');
    const downloadQrPngBtn = document.getElementById('downloadQrPngBtn');

    function getBaseUrl() {
        const url = new URL(window.location.href);
        url.search = '';
        url.hash = '';
        return url.toString();
    }

    function buildTargetUrl(targetType) {
        const base = getBaseUrl();
        if (targetType === 'whatsapp') {
            return `https://wa.me/${waNumber}?text=${encodeURIComponent('Olá João! Escaneei seu QR Code e gostaria de conversar sobre um projeto.')}`;
        }
        if (targetType === 'recruiter') {
            return `${base}?mode=recruiter&utm_source=qrcode`;
        }
        return `${base}?mode=business&utm_source=qrcode`;
    }

    function renderQrCode(textUrl) {
        if (!qrContainer || typeof QRCode === 'undefined') return;
        qrContainer.innerHTML = '';
        new QRCode(qrContainer, {
            text: textUrl,
            width: 186,
            height: 186,
            colorDark: '#0F172A',
            colorLight: '#FFFFFF',
            correctLevel: QRCode.CorrectLevel.H,
        });
    }

    function selectQrPreset(targetType) {
        qrTargetButtons.forEach((b) => {
            b.classList.toggle('active', b.dataset.qrTarget === targetType);
        });

        if (qrCardSubtitle) {
            if (targetType === 'whatsapp') {
                qrCardSubtitle.textContent = 'Escaneie para chamar direto no WhatsApp';
            } else if (targetType === 'recruiter') {
                qrCardSubtitle.textContent = 'Portfólio Full-Stack • PHP | Java | React | Python';
            } else {
                qrCardSubtitle.textContent = 'Soluções em Automação, IA & Sistemas Web';
            }
        }

        const targetUrl = buildTargetUrl(targetType);
        if (qrCustomUrlInput) {
            qrCustomUrlInput.value = targetUrl;
        }
        renderQrCode(targetUrl);
    }

    qrTargetButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            selectQrPreset(btn.dataset.qrTarget);
        });
    });

    qrCustomUrlInput?.addEventListener('input', (e) => {
        const val = e.target.value.trim();
        if (val) {
            renderQrCode(val);
        }
    });

    copyQrLinkBtn?.addEventListener('click', async () => {
        if (!qrCustomUrlInput) return;
        try {
            await navigator.clipboard.writeText(qrCustomUrlInput.value);
            copyQrLinkBtn.innerHTML = '<i class="bi bi-check2 text-success"></i>';
            setTimeout(() => {
                copyQrLinkBtn.innerHTML = '<i class="bi bi-clipboard"></i>';
            }, 1800);
        } catch (_) {
            qrCustomUrlInput.select();
            document.execCommand('copy');
        }
    });

    downloadQrPngBtn?.addEventListener('click', () => {
        const canvas = qrContainer?.querySelector('canvas');
        const img = qrContainer?.querySelector('img');
        let dataUrl = '';
        if (canvas) {
            dataUrl = canvas.toDataURL('image/png');
        } else if (img && img.src) {
            dataUrl = img.src;
        }
        if (!dataUrl) return;

        const link = document.createElement('a');
        link.download = 'QRCode_Joao_Gabriel.png';
        link.href = dataUrl;
        document.body.appendChild(link);
        link.click();
        link.remove();
    });

    // Inicializa o QR Code no preset correspondente ao modo atual
    selectQrPreset(body.dataset.activeMode === 'recruiter' ? 'recruiter' : 'business');
});
