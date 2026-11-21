<?php
declare(strict_types=1);

$data = require __DIR__ . '/config/data.php';
$profile = $data['profile'];
$contacts = $data['contacts'];

// Download do vCard (.vcf) para salvar na agenda do celular com 1 toque após escanear o QR Code
if (isset($_GET['download']) && $_GET['download'] === 'vcard') {
    $vcard = [
        'BEGIN:VCARD',
        'VERSION:3.0',
        'N:Oliveira;João Gabriel;Santana de;;',
        'FN:' . $profile['name'],
        'TITLE:Desenvolvedor Full-Stack & Consultor de Automação/IA',
        'TEL;TYPE=CELL,VOICE:+5511988555937',
        'EMAIL;TYPE=INTERNET,PREF:' . $contacts['email'],
        'ADR;TYPE=WORK:;;Santo André;SP;;Brasil',
        'NOTE:Soluções em Sistemas Web, Automação de Processos (RPA) e Atendimento com IA no WhatsApp.',
        'END:VCARD',
    ];

    header('Content-Type: text/vcard; charset=utf-8');
    header('Content-Disposition: attachment; filename="Joao_Gabriel_Oliveira.vcf"');
    echo implode("\r\n", $vcard);
    exit;
}

// Endpoint AJAX para o Simulador / Pré-Orçamento Rápido (salva log local opcional e retorna link do WhatsApp formatado)
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_GET['action']) && $_GET['action'] === 'quick_quote') {
    header('Content-Type: application/json; charset=utf-8');

    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput ?: '{}', true) ?? [];

    $clientName = trim(strip_tags((string)($payload['name'] ?? '')));
    $clientCompany = trim(strip_tags((string)($payload['company'] ?? '')));
    $serviceType = trim(strip_tags((string)($payload['service'] ?? 'Solução sob medida')));
    $urgency = trim(strip_tags((string)($payload['urgency'] ?? 'Ainda este mês')));
    $details = trim(strip_tags((string)($payload['details'] ?? '')));

    $lines = [
        "Olá João! Tudo bem?",
        "Vi sua página e gostaria de solicitar um orçamento rápido:",
        "",
        "• *Nome:* " . ($clientName !== '' ? $clientName : 'Não informado'),
    ];
    if ($clientCompany !== '') {
        $lines[] = "• *Empresa/Ramo:* " . $clientCompany;
    }
    $lines[] = "• *Solução de interesse:* " . $serviceType;
    $lines[] = "• *Prazo desejado:* " . $urgency;
    if ($details !== '') {
        $lines[] = "• *Resumo da necessidade:* " . $details;
    }

    $message = implode("\n", $lines);
    $waUrl = 'https://wa.me/' . $contacts['whatsapp_number'] . '?text=' . rawurlencode($message);

    // Registro local seguro de leads para controle do João (ignorado no git)
    $leadsDir = __DIR__ . '/storage/leads';
    if (!is_dir($leadsDir)) {
        @mkdir($leadsDir, 0755, true);
    }
    $leadEntry = [
        'timestamp' => date('c'),
        'name' => $clientName,
        'company' => $clientCompany,
        'service' => $serviceType,
        'urgency' => $urgency,
        'details' => $details,
        'utm_source' => strip_tags((string)($payload['utm_source'] ?? 'direct')),
    ];
    @file_put_contents(
        $leadsDir . '/leads_' . date('Y_m') . '.jsonl',
        json_encode($leadEntry, JSON_UNESCAPED_UNICODE) . "\n",
        FILE_APPEND | LOCK_EX
    );

    echo json_encode([
        'status' => 'ok',
        'whatsapp_url' => $waUrl,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Determina modo inicial: 'business' (padrão para QR Code comercial) ou 'recruiter' (para recrutadores/bot Linux)
$requestedMode = isset($_GET['mode']) ? strtolower(trim((string)$_GET['mode'])) : 'business';
$activeMode = in_array($requestedMode, ['business', 'recruiter'], true) ? $requestedMode : 'business';

function e(string $val): string
{
    return htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function waLink(string $phone, string $message): string
{
    return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($profile['short_name']) ?> | Soluções em Software, IA & Portfólio Full-Stack</title>
    <meta name="description" content="<?= e($profile['summary_business']) ?>">
    <meta name="theme-color" content="#F8FAFC">

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom Light & Friendly Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-active-mode="<?= e($activeMode) ?>"
      data-wa-number="<?= e($contacts['whatsapp_number']) ?>"
      data-email="<?= e($contacts['email']) ?>">

    <!-- Top Navbar -->
    <header class="navbar-light-glass sticky-top">
        <div class="container py-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <a href="?" class="brand-logo text-decoration-none d-flex align-items-center gap-2">
                <span class="brand-avatar">JG</span>
                <div>
                    <span class="fw-bold text-slate-900 d-block lh-1"><?= e($profile['short_name']) ?></span>
                    <small class="text-slate-500" style="font-size: 0.75rem;">Santo André • ABC / SP</small>
                </div>
            </a>

            <!-- Seletor Híbrido Principal (Empresas vs Recrutadores) -->
            <div class="mode-switcher" role="tablist" aria-label="Alternar visão da página">
                <button type="button"
                        class="mode-btn <?= $activeMode === 'business' ? 'active' : '' ?>"
                        data-switch-mode="business">
                    <i class="bi bi-briefcase-fill me-1"></i>
                    <span>Para Empresas</span>
                </button>
                <button type="button"
                        class="mode-btn <?= $activeMode === 'recruiter' ? 'active' : '' ?>"
                        data-switch-mode="recruiter">
                    <i class="bi bi-code-slash me-1"></i>
                    <span>Para Recrutadores</span>
                </button>
            </div>

            <!-- Botões Rápidos Direita -->
            <div class="d-flex align-items-center gap-2">
                <button type="button"
                        class="btn btn-light-pill btn-sm d-flex align-items-center gap-1"
                        data-bs-toggle="modal"
                        data-bs-target="#qrCodeModal"
                        title="Abrir QR Code da página">
                    <i class="bi bi-qr-code text-primary"></i>
                    <span class="d-none d-md-inline">QR Code</span>
                </button>

                <a href="<?= e(waLink($contacts['whatsapp_number'], 'Olá João! Vi sua página e gostaria de conversar.')) ?>"
                   target="_blank"
                   rel="noopener"
                   class="btn btn-whatsapp-nav btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-whatsapp"></i>
                    <span class="d-none d-sm-inline">WhatsApp</span>
                </a>
            </div>
        </div>
    </header>

    <!-- =======================================================================
         VISÃO 1: PARA EMPRESAS & NEGÓCIOS LOCAIS (FOCO COMERCIAL / RENDA EXTRA)
         Regra de Contatos: Exibe apenas WhatsApp e E-mail (+ Salvar na Agenda)
         ======================================================================= -->
    <main id="view-business" class="mode-view <?= $activeMode === 'business' ? 'is-visible' : 'd-none' ?>">

        <!-- Hero Comercial -->
        <section class="hero-section py-5">
            <div class="container py-lg-3">
                <div class="row align-items-center g-4 g-lg-5">
                    <div class="col-lg-7">
                        <div class="d-inline-flex align-items-center gap-2 badge-soft-blue mb-3">
                            <span class="status-dot"></span>
                            <span>Atendimento Rápido em Santo André, ABC & Remoto</span>
                        </div>

                        <h1 class="hero-title mb-3">
                            Automatize seu atendimento, elimine planilhas manuais e <span class="text-gradient-blue">venda mais com tecnologia</span>.
                        </h1>

                        <p class="hero-subtitle mb-4">
                            <?= e($profile['summary_business']) ?>
                            Soluções práticas, sem mensalidades abusivas e entregues sob medida para a realidade do seu negócio.
                        </p>

                        <!-- CTAs Principais (Apenas WhatsApp, E-mail e Agenda conforme solicitado) -->
                        <div class="d-flex flex-wrap gap-3 mb-4">
                            <a href="<?= e(waLink($contacts['whatsapp_number'], 'Olá João! Escaneei seu contato / vi sua página e gostaria de fazer um orçamento para minha empresa.')) ?>"
                               target="_blank"
                               rel="noopener"
                               class="btn btn-whatsapp-lg shadow-sm">
                                <i class="bi bi-whatsapp fs-5 me-2"></i>
                                Chamar no WhatsApp Agora
                            </a>

                            <a href="?download=vcard" class="btn btn-outline-slate-lg">
                                <i class="bi bi-person-plus-fill me-2 text-primary"></i>
                                Salvar Contato na Agenda
                            </a>
                        </div>

                        <!-- Faixa de Contatos Diretos (Somente WhatsApp e E-mail) -->
                        <div class="contact-strip-light d-flex flex-wrap align-items-center gap-3">
                            <a href="<?= e(waLink($contacts['whatsapp_number'], 'Olá João! Gostaria de solicitar um orçamento.')) ?>"
                               target="_blank"
                               rel="noopener"
                               class="contact-pill">
                                <i class="bi bi-whatsapp text-success"></i>
                                <span><?= e($contacts['whatsapp_display']) ?></span>
                            </a>
                            <a href="mailto:<?= e($contacts['email']) ?>?subject=Or%C3%A7amento%20de%20Servi%C3%A7os%20-%20Tecnologia%20e%20Automa%C3%A7%C3%A3o"
                               class="contact-pill">
                                <i class="bi bi-envelope-fill text-primary"></i>
                                <span><?= e($contacts['email']) ?></span>
                            </a>
                        </div>
                    </div>

                    <!-- Card Lateral: Orçamento Rápido em 30 Segundos -->
                    <div class="col-lg-5">
                        <div class="card-clean-elevated p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary fw-semibold mb-1">Orçamento sem compromisso</span>
                                    <h2 class="h5 fw-bold text-slate-900 mb-0">Do que sua empresa precisa hoje?</h2>
                                </div>
                                <div class="icon-circle-soft">
                                    <i class="bi bi-lightning-charge-fill text-primary fs-5"></i>
                                </div>
                            </div>

                            <form id="quickQuoteForm" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="qName" class="form-label small fw-semibold text-slate-700">Seu nome</label>
                                    <input type="text" class="form-control form-control-light" id="qName" placeholder="Ex: Carlos Silva" required>
                                </div>

                                <div class="mb-3">
                                    <label for="qCompany" class="form-label small fw-semibold text-slate-700">Empresa ou ramo (opcional)</label>
                                    <input type="text" class="form-control form-control-light" id="qCompany" placeholder="Ex: Clínica Odontológica / Escritório / Loja">
                                </div>

                                <div class="mb-3">
                                    <label for="qService" class="form-label small fw-semibold text-slate-700">Qual solução você busca?</label>
                                    <select class="form-select form-control-light" id="qService">
                                        <option value="Atendimento Automático 24h (WhatsApp / IA)">🤖 Robô de Atendimento no WhatsApp / IA</option>
                                        <option value="Automação de Planilhas e Processos (Python / Excel)">⚙️ Automação de Planilhas e Rotinas (Excel / RPA)</option>
                                        <option value="Landing Page / Site Profissional">🚀 Criação de Landing Page / Site de Vendas</option>
                                        <option value="Sistema Web Sob Medida / Integração CRM">💻 Sistema Web Sob Medida / Integração de Sistemas</option>
                                        <option value="Consultoria Técnica / Outra necessidade">💡 Outro projeto ou dúvida</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="qDetails" class="form-label small fw-semibold text-slate-700">Resumo rápido (opcional)</label>
                                    <textarea class="form-control form-control-light" id="qDetails" rows="2" placeholder="Conte em 1 frase o que você quer melhorar..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary-gradient w-100 py-2 fw-semibold">
                                    <i class="bi bi-send-fill me-2"></i>
                                    Enviar Pedido direto no WhatsApp
                                </button>
                                <p class="text-center text-slate-500 small mb-0 mt-2">
                                    <i class="bi bi-clock me-1"></i> Resposta rápida direto comigo, sem intermediários.
                                </p>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Métricas de Impacto -->
                <div class="row g-3 mt-4 pt-2">
                    <?php foreach ($data['business_metrics'] as $metric): ?>
                        <div class="col-6 col-md-3">
                            <div class="metric-card-light p-3 h-100">
                                <div class="metric-value"><?= e($metric['value']) ?></div>
                                <div class="metric-label"><?= e($metric['label']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Catálogo de Soluções Práticas -->
        <section class="py-5 bg-white border-top border-bottom">
            <div class="container">
                <div class="text-center max-w-700 mx-auto mb-5">
                    <span class="section-eyebrow">Soluções Práticas para o seu Negócio</span>
                    <h2 class="section-title mt-1">Como posso ajudar sua empresa a economizar tempo e faturar mais</h2>
                    <p class="text-slate-600 mb-0">
                        Experiência real construindo sistemas para grandes operações clínicas e industriais, agora acessível para empresas da nossa região.
                    </p>
                </div>

                <div class="row g-4">
                    <?php foreach ($data['business_services'] as $service): ?>
                        <div class="col-md-6">
                            <div class="service-card-light h-100 d-flex flex-column justify-content-between p-4">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <span class="service-icon-box icon-<?= e($service['color']) ?>">
                                            <i class="bi <?= e($service['icon']) ?>"></i>
                                        </span>
                                        <span class="badge-pill-soft"><?= e($service['badge']) ?></span>
                                    </div>

                                    <h3 class="h5 fw-bold text-slate-900 mb-2"><?= e($service['title']) ?></h3>

                                    <div class="problem-callout mb-3">
                                        <i class="bi bi-question-circle-fill text-primary me-1"></i>
                                        <span><?= e($service['problem']) ?></span>
                                    </div>

                                    <p class="text-slate-600 small mb-3">
                                        <?= e($service['description']) ?>
                                    </p>

                                    <ul class="list-unstyled service-checklist mb-4">
                                        <?php foreach ($service['deliverables'] as $item): ?>
                                            <li>
                                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                <span><?= e($item) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>

                                <div class="pt-3 border-top d-flex align-items-center justify-content-between gap-2">
                                    <a href="<?= e(waLink($contacts['whatsapp_number'], $service['whatsapp_msg'])) ?>"
                                       target="_blank"
                                       rel="noopener"
                                       class="btn btn-outline-primary-clean btn-sm w-100 fw-semibold">
                                        <i class="bi bi-whatsapp me-1"></i> Solicitar Orçamento desta Solução
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Como Funciona (3 Passos Simples) -->
        <section class="py-5">
            <div class="container">
                <div class="text-center max-w-700 mx-auto mb-5">
                    <span class="section-eyebrow">Simples, Transparente e Sem Burocracia</span>
                    <h2 class="section-title mt-1">Como funciona o nosso trabalho</h2>
                </div>

                <div class="row g-4">
                    <?php foreach ($data['business_steps'] as $step): ?>
                        <div class="col-md-4">
                            <div class="step-card-light p-4 h-100">
                                <span class="step-number"><?= e($step['step']) ?></span>
                                <h3 class="h5 fw-bold text-slate-900 mt-3 mb-2"><?= e($step['title']) ?></h3>
                                <p class="text-slate-600 small mb-0"><?= e($step['desc']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Banner Final de Contato Comercial (Apenas WhatsApp e E-mail) -->
                <div class="cta-banner-light mt-5 p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="badge bg-white text-primary fw-semibold mb-2 shadow-xs">
                                <i class="bi bi-geo-alt-fill me-1"></i> Santo André & Região do ABC
                            </span>
                            <h3 class="h3 fw-bold text-slate-900 mb-2">
                                Vamos conversar sobre como destravar o crescimento do seu negócio?
                            </h3>
                            <p class="text-slate-600 mb-0">
                                Me mande uma mensagem no WhatsApp ou e-mail. Explico tudo de forma simples e encontramos a melhor solução para o seu bolso.
                            </p>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <div class="d-grid gap-2">
                                <a href="<?= e(waLink($contacts['whatsapp_number'], 'Olá João! Vi sua página de serviços e gostaria de bater um papo sobre uma solução para minha empresa.')) ?>"
                                   target="_blank"
                                   rel="noopener"
                                   class="btn btn-whatsapp-lg">
                                    <i class="bi bi-whatsapp me-2"></i>
                                    <?= e($contacts['whatsapp_display']) ?>
                                </a>
                                <a href="mailto:<?= e($contacts['email']) ?>?subject=Contato%20Comercial%20-%20Solu%C3%A7%C3%B5es%20em%20Tecnologia"
                                   class="btn btn-light-border">
                                    <i class="bi bi-envelope-fill text-primary me-2"></i>
                                    <?= e($contacts['email']) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- =======================================================================
         VISÃO 2: PARA RECRUTADORES & TECH LEADS (PORTFÓLIO & CURRÍCULO COMPLETO)
         Regra de Contatos: Exibe TODOS (WhatsApp, E-mail, LinkedIn, GitHub e PDF CV)
         ======================================================================= -->
    <main id="view-recruiter" class="mode-view <?= $activeMode === 'recruiter' ? 'is-visible' : 'd-none' ?>">

        <!-- Hero Recrutador -->
        <section class="hero-section py-5">
            <div class="container py-lg-2">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <div class="d-inline-flex align-items-center gap-2 badge-soft-emerald mb-3">
                            <span class="status-dot-green"></span>
                            <span><?= e($profile['availability_recruiter']) ?></span>
                        </div>

                        <h1 class="hero-title mb-2">
                            <?= e($profile['name']) ?>
                        </h1>
                        <p class="h5 fw-semibold text-primary mb-3">
                            <?= e($profile['role_recruiter']) ?>
                        </p>

                        <p class="text-slate-600 mb-4">
                            <?= e($profile['summary_recruiter']) ?>
                        </p>

                        <!-- Botões de Ação Completa para Recrutadores -->
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <a href="<?= e($contacts['cv_file']) ?>"
                               download="Curriculo_Joao_Gabriel_Santana_PT.pdf"
                               class="btn btn-primary-gradient">
                                <i class="bi bi-file-earmark-arrow-down-fill me-2"></i>
                                Baixar Currículo (PDF)
                            </a>

                            <a href="<?= e($contacts['linkedin_url']) ?>"
                               target="_blank"
                               rel="noopener"
                               class="btn btn-outline-slate">
                                <i class="bi bi-linkedin text-primary me-1"></i>
                                LinkedIn
                            </a>

                            <a href="<?= e($contacts['github_url']) ?>"
                               target="_blank"
                               rel="noopener"
                               class="btn btn-outline-slate">
                                <i class="bi bi-github me-1"></i>
                                GitHub
                            </a>

                            <a href="<?= e(waLink($contacts['whatsapp_number'], 'Olá João Gabriel! Vi seu portfólio técnico e gostaria de conversar sobre uma oportunidade Full-Stack.')) ?>"
                               target="_blank"
                               rel="noopener"
                               class="btn btn-outline-slate">
                                <i class="bi bi-whatsapp text-success me-1"></i>
                                WhatsApp
                            </a>
                        </div>

                        <!-- Todos os Contatos Visíveis para Recrutadores -->
                        <div class="contact-strip-light d-flex flex-wrap align-items-center gap-3">
                            <a href="mailto:<?= e($contacts['email']) ?>" class="contact-pill">
                                <i class="bi bi-envelope-fill text-primary"></i>
                                <span><?= e($contacts['email']) ?></span>
                            </a>
                            <a href="<?= e(waLink($contacts['whatsapp_number'], 'Olá João Gabriel! Tudo bem?')) ?>" target="_blank" rel="noopener" class="contact-pill">
                                <i class="bi bi-telephone-fill text-success"></i>
                                <span><?= e($contacts['whatsapp_display']) ?></span>
                            </a>
                            <a href="<?= e($contacts['linkedin_url']) ?>" target="_blank" rel="noopener" class="contact-pill">
                                <i class="bi bi-linkedin text-primary"></i>
                                <span><?= e($contacts['linkedin_display']) ?></span>
                            </a>
                            <a href="<?= e($contacts['github_url']) ?>" target="_blank" rel="noopener" class="contact-pill">
                                <i class="bi bi-github text-slate-800"></i>
                                <span><?= e($contacts['github_display']) ?></span>
                            </a>
                        </div>
                    </div>

                    <!-- Resumo de Diferenciais de Engenharia -->
                    <div class="col-lg-4">
                        <div class="card-clean-elevated p-4">
                            <h2 class="h6 fw-bold text-uppercase text-slate-500 mb-3">
                                <i class="bi bi-shield-check text-primary me-1"></i> Diferenciais de Produção
                            </h2>
                            <ul class="list-unstyled d-grid gap-3 mb-0 small">
                                <li class="d-flex gap-2">
                                    <i class="bi bi-database-fill-gear text-primary fs-5"></i>
                                    <div>
                                        <strong class="text-slate-900 d-block">Arquitetura Multi-Database & Tuning</strong>
                                        <span class="text-slate-600">MySQL e Oracle com processamento em <em>chunks</em>, <em>dirty checking</em> e migrations seguras.</span>
                                    </div>
                                </li>
                                <li class="d-flex gap-2">
                                    <i class="bi bi-mic-fill text-primary fs-5"></i>
                                    <div>
                                        <strong class="text-slate-900 d-block">IA de Voz & Mensageria em Escala</strong>
                                        <span class="text-slate-600">Integrações REST/Webhooks com Vapi, LiveKit, OpenAI API, WhatsApp Business API e Olos/Voll.</span>
                                    </div>
                                </li>
                                <li class="d-flex gap-2">
                                    <i class="bi bi-globe2 text-primary fs-5"></i>
                                    <div>
                                        <strong class="text-slate-900 d-block">Inglês Fluente & Compliance Global</strong>
                                        <span class="text-slate-600">Experiência em rollout global SAP PM junto à matriz na Alemanha (KOSTAL) e RPA corporativo.</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Matriz de Competências Técnicas -->
        <section class="py-5 bg-white border-top border-bottom">
            <div class="container">
                <div class="mb-4">
                    <span class="section-eyebrow">Stack Tecnológica</span>
                    <h2 class="section-title mt-1">Principais Competências Técnicas</h2>
                </div>

                <div class="row g-4">
                    <?php foreach ($data['tech_skills'] as $skillGroup): ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="skill-box-light p-4 h-100">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="service-icon-box icon-primary">
                                        <i class="bi <?= e($skillGroup['icon']) ?>"></i>
                                    </span>
                                    <h3 class="h6 fw-bold text-slate-900 mb-0"><?= e($skillGroup['category']) ?></h3>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($skillGroup['items'] as $skill): ?>
                                        <span class="tech-tag"><?= e($skill) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Experiência Profissional & Projeto em Destaque -->
        <section class="py-5">
            <div class="container">
                <div class="row g-5">
                    <!-- Coluna Experiência -->
                    <div class="col-lg-7">
                        <span class="section-eyebrow">Trajetória Corporativa</span>
                        <h2 class="section-title mt-1 mb-4">Experiência Profissional</h2>

                        <div class="timeline-light">
                            <?php foreach ($data['experiences'] as $exp): ?>
                                <div class="timeline-item-light p-4 mb-4">
                                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                        <div>
                                            <h3 class="h5 fw-bold text-slate-900 mb-0"><?= e($exp['role']) ?></h3>
                                            <span class="fw-semibold text-primary"><?= e($exp['company']) ?></span>
                                            <span class="text-slate-500 small"> • <?= e($exp['location']) ?></span>
                                        </div>
                                        <span class="badge-pill-soft"><?= e($exp['period']) ?></span>
                                    </div>

                                    <ul class="text-slate-600 small ps-3 mb-3 d-grid gap-2">
                                        <?php foreach ($exp['highlights'] as $bullet): ?>
                                            <li><?= e($bullet) ?></li>
                                        <?php endforeach; ?>
                                    </ul>

                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach ($exp['tags'] as $tag): ?>
                                            <span class="tech-tag-sm"><?= e($tag) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Coluna Projetos em Destaque & Formação -->
                    <div class="col-lg-5">
                        <span class="section-eyebrow">Engenharia & Código</span>
                        <h2 class="section-title mt-1 mb-4">Projeto em Destaque</h2>

                        <?php foreach ($data['featured_projects'] as $proj): ?>
                            <div class="card-clean-elevated p-4 mb-4">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                    <div>
                                        <span class="badge bg-primary-subtle text-primary fw-semibold mb-1">Java 21 + Spring Boot + Oracle + React 19</span>
                                        <h3 class="h5 fw-bold text-slate-900 mb-0"><?= e($proj['title']) ?></h3>
                                        <p class="text-slate-500 small mb-2"><?= e($proj['subtitle']) ?></p>
                                    </div>
                                </div>

                                <ul class="text-slate-600 small ps-3 mb-3 d-grid gap-2">
                                    <?php foreach ($proj['bullets'] as $b): ?>
                                        <li><?= e($b) ?></li>
                                    <?php endforeach; ?>
                                </ul>

                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    <?php foreach ($proj['stack'] as $st): ?>
                                        <span class="tech-tag-sm"><?= e($st) ?></span>
                                    <?php endforeach; ?>
                                </div>

                                <a href="<?= e($proj['repo_url']) ?>"
                                   target="_blank"
                                   rel="noopener"
                                   class="btn btn-outline-primary-clean btn-sm w-100 fw-semibold">
                                    <i class="bi bi-github me-1"></i> Ver Repositório no GitHub (<?= e($proj['repo_display']) ?>)
                                </a>
                            </div>
                        <?php endforeach; ?>

                        <!-- Formação Acadêmica & Idiomas -->
                        <div class="card-clean-elevated p-4">
                            <h3 class="h6 fw-bold text-uppercase text-slate-500 mb-3">
                                <i class="bi bi-mortarboard-fill text-primary me-1"></i> Formação Acadêmica & Idiomas
                            </h3>

                            <?php foreach ($data['education'] as $edu): ?>
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <strong class="text-slate-900 small"><?= e($edu['degree']) ?></strong>
                                        <span class="badge-pill-soft"><?= e($edu['period']) ?></span>
                                    </div>
                                    <div class="text-primary small fw-semibold"><?= e($edu['institution']) ?></div>
                                    <p class="text-slate-600 small mb-0 mt-1"><?= e($edu['details']) ?></p>
                                </div>
                            <?php endforeach; ?>

                            <div>
                                <strong class="text-slate-900 small d-block mb-2">Idiomas</strong>
                                <?php foreach ($data['languages'] as $lang): ?>
                                    <div class="small text-slate-600 mb-1">
                                        <i class="bi bi-translate text-primary me-1"></i>
                                        <strong><?= e($lang['lang']) ?>:</strong> <?= e($lang['level']) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Rodapé Adaptativo (Respeita a regra de contatos de acordo com a aba ativa) -->
    <footer class="footer-light py-4 border-top bg-white">
        <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <div>
                <span class="fw-bold text-slate-900"><?= e($profile['name']) ?></span>
                <span class="text-slate-500 small d-block">
                    <?= e($profile['location']) ?>
                </span>
            </div>

            <!-- Links de Rodapé para Visão Empresas (Apenas WhatsApp e E-mail) -->
            <div class="footer-contacts-business <?= $activeMode === 'business' ? 'd-flex' : 'd-none' ?> flex-wrap align-items-center gap-3 small">
                <a href="<?= e(waLink($contacts['whatsapp_number'], 'Olá João! Vi sua página e gostaria de conversar.')) ?>"
                   target="_blank"
                   rel="noopener"
                   class="text-decoration-none text-slate-700 fw-medium">
                    <i class="bi bi-whatsapp text-success me-1"></i><?= e($contacts['whatsapp_display']) ?>
                </a>
                <a href="mailto:<?= e($contacts['email']) ?>"
                   class="text-decoration-none text-slate-700 fw-medium">
                    <i class="bi bi-envelope-fill text-primary me-1"></i><?= e($contacts['email']) ?>
                </a>
                <button type="button"
                        class="btn btn-link btn-sm text-decoration-none p-0 fw-semibold"
                        data-bs-toggle="modal"
                        data-bs-target="#qrCodeModal">
                    <i class="bi bi-qr-code me-1"></i>Exibir QR Code
                </button>
            </div>

            <!-- Links de Rodapé para Visão Recrutadores (Todos os contatos) -->
            <div class="footer-contacts-recruiter <?= $activeMode === 'recruiter' ? 'd-flex' : 'd-none' ?> flex-wrap align-items-center gap-3 small">
                <a href="<?= e(waLink($contacts['whatsapp_number'], 'Olá João Gabriel!')) ?>" target="_blank" rel="noopener" class="text-decoration-none text-slate-700">
                    <i class="bi bi-whatsapp text-success me-1"></i><?= e($contacts['whatsapp_display']) ?>
                </a>
                <a href="mailto:<?= e($contacts['email']) ?>" class="text-decoration-none text-slate-700">
                    <i class="bi bi-envelope-fill text-primary me-1"></i><?= e($contacts['email']) ?>
                </a>
                <a href="<?= e($contacts['linkedin_url']) ?>" target="_blank" rel="noopener" class="text-decoration-none text-slate-700">
                    <i class="bi bi-linkedin text-primary me-1"></i>LinkedIn
                </a>
                <a href="<?= e($contacts['github_url']) ?>" target="_blank" rel="noopener" class="text-decoration-none text-slate-700">
                    <i class="bi bi-github me-1"></i>GitHub
                </a>
            </div>
        </div>
    </footer>

    <!-- Botão Flutuante de WhatsApp + QR Code (Mobile Friendly) -->
    <div class="floating-actions">
        <button type="button"
                class="fab-qr shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#qrCodeModal"
                title="Mostrar QR Code">
            <i class="bi bi-qr-code"></i>
        </button>
        <a href="<?= e(waLink($contacts['whatsapp_number'], 'Olá João! Vi sua página e gostaria de conversar.')) ?>"
           id="floatingWaBtn"
           target="_blank"
           rel="noopener"
           class="fab-whatsapp shadow"
           title="Chamar no WhatsApp">
            <i class="bi bi-whatsapp"></i>
            <span class="fab-label">Falar no WhatsApp</span>
        </a>
    </div>

    <!-- =======================================================================
         MODAL DE QR CODE & CARTÃO DIGITAL (PRONTO PARA IMPRESSÃO OU TELA)
         ======================================================================= -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-clean-light">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <span class="badge bg-primary-subtle text-primary fw-semibold mb-1">Acesso Rápido via Câmera</span>
                        <h2 class="modal-title h5 fw-bold text-slate-900" id="qrCodeModalLabel">
                            QR Code — <?= e($profile['short_name']) ?>
                        </h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body text-center pt-3">
                    <!-- Escolha do destino do QR Code -->
                    <div class="btn-group w-100 mb-3" role="group" aria-label="Tipo de QR Code">
                        <button type="button" class="btn btn-outline-primary btn-sm active" data-qr-target="business">
                            🏢 Página Empresas
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-qr-target="whatsapp">
                            💬 Direto WhatsApp
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-qr-target="recruiter">
                            💻 Portfólio Recrutador
                        </button>
                    </div>

                    <!-- Área do Cartão Imprimível -->
                    <div id="printableQrCard" class="qr-card-box p-4 mx-auto">
                        <div class="fw-bold text-slate-900 mb-1"><?= e($profile['short_name']) ?></div>
                        <div id="qrCardSubtitle" class="text-primary small fw-semibold mb-3">
                            Soluções em Automação, IA & Sistemas Web
                        </div>

                        <div id="qrCanvasContainer" class="qr-canvas-wrapper mx-auto mb-3"></div>

                        <div class="small text-slate-600 mb-1">
                            <i class="bi bi-whatsapp text-success me-1"></i><?= e($contacts['whatsapp_display']) ?>
                        </div>
                        <div class="small text-slate-500">
                            <i class="bi bi-envelope me-1"></i><?= e($contacts['email']) ?>
                        </div>
                    </div>

                    <!-- URL Editável (caso queira gerar o QR Code para o domínio final antes mesmo de publicar) -->
                    <div class="mt-3 text-start">
                        <label for="qrCustomUrl" class="form-label small fw-semibold text-slate-700 mb-1">
                            Link codificado no QR Code (você pode editar para o seu domínio final):
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="qrCustomUrl" class="form-control form-control-light" value="">
                            <button class="btn btn-outline-secondary" type="button" id="copyQrLinkBtn" title="Copiar Link">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
                    <a href="?download=vcard" class="btn btn-outline-slate btn-sm">
                        <i class="bi bi-person-vcard me-1"></i> Baixar vCard (.vcf)
                    </a>
                    <button type="button" id="downloadQrPngBtn" class="btn btn-primary-gradient btn-sm">
                        <i class="bi bi-download me-1"></i> Baixar QR Code (PNG)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & QRCode.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
