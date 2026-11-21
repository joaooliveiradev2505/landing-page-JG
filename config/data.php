<?php
declare(strict_types=1);

return [
    'profile' => [
        'name' => 'João Gabriel Santana de Oliveira',
        'short_name' => 'João Gabriel',
        'role_business' => 'Consultor de Tecnologia, Automação & Sistemas Sob Medida',
        'role_recruiter' => 'Desenvolvedor Full-Stack (PHP Laravel | Java Spring Boot | React.js | Python)',
        'location' => 'Santo André, SP — Região do ABC & Grande SP',
        'availability_business' => 'Atendimento presencial no ABC ou remoto em todo o Brasil',
        'availability_recruiter' => 'Disponibilidade imediata (CLT / PJ • Presencial, Híbrido ou Remoto)',
        'summary_recruiter' => 'Desenvolvedor Full-Stack graduando em Análise e Desenvolvimento de Sistemas (FIAP), com sólida base analítica proveniente de 4 anos de formação em Engenharia de Computação. Experiência prática no desenvolvimento, manutenção evolutiva e arquitetura de sistemas corporativos de grande porte (ERP/CRM e plataformas clínicas), além da construção de aplicações Full-Stack no ecossistema PHP (Laravel / Eloquent), Java 21 (Spring Boot), React.js e Python. Domínio em APIs RESTful, microsserviços de IA de Voz (Vapi, LiveKit, OpenAI API), automação de mensageria (WhatsApp Business API, RCS) e modelagem/otimização de bancos de dados relacionais (MySQL e Oracle Database). Inglês avançado/fluente.',
        'summary_business' => 'Ajudo clínicas, escritórios, comércios e empresas do ABC a venderem mais, automatizarem o atendimento no WhatsApp com Inteligência Artificial e eliminarem tarefas manuais repetitivas em planilhas e sistemas.',
    ],

    // Regra de Exibição de Contatos:
    // - Visão Empresas (business): Apenas WhatsApp e E-mail (+ vCard)
    // - Visão Recrutadores (recruiter): Todos (WhatsApp, E-mail, LinkedIn, GitHub, Currículo PDF)
    'contacts' => [
        'whatsapp_display' => '+55 (11) 98855-5937',
        'whatsapp_number' => '5511988555937',
        'email' => 'joaooliveiradev.2505@gmail.com',
        'linkedin_display' => 'linkedin.com/in/joao-gabriel-santana-de-oliveira-273a2a220',
        'linkedin_url' => 'https://www.linkedin.com/in/joao-gabriel-santana-de-oliveira-273a2a220',
        'github_display' => 'github.com/joaooliveiradev2505',
        'github_url' => 'https://github.com/joaooliveiradev2505',
        'cv_file' => 'assets/docs/Curriculo_Joao_Gabriel_Santana_PT.pdf',
    ],

    'business_metrics' => [
        ['value' => '24/7', 'label' => 'Atendimento automático no WhatsApp & Voz'],
        ['value' => '-80%', 'label' => 'Redução de tempo em planilhas e rotinas manuais'],
        ['value' => '100%', 'label' => 'Soluções sob medida para o seu negócio no ABC'],
        ['value' => 'Rápido', 'label' => 'Entregas ágeis para gerar resultado ainda este mês'],
    ],

    'business_services' => [
        [
            'id' => 'ia-whatsapp',
            'badge' => 'Alta Demanda • Clínicas & Comércios',
            'icon' => 'bi-robot',
            'color' => 'primary',
            'title' => 'Atendimento Automático 24h (WhatsApp & Voz com IA)',
            'problem' => 'Perdendo clientes porque demora para responder no WhatsApp ou fora do horário comercial?',
            'description' => 'Implementação de assistentes inteligentes no WhatsApp Oficial e agentes de voz por telefone que respondem dúvidas, qualificam clientes, enviam catálogos e agendam horários automaticamente.',
            'deliverables' => [
                'Integração oficial com WhatsApp Business API, RCS/SMS e Chatbots',
                'Agentes de voz com IA que atendem e confirmam agendamentos',
                'Distribuição automática de clientes para sua equipe comercial ou CRM',
            ],
            'whatsapp_msg' => 'Olá João! Vi sua página e gostaria de saber mais sobre Atendimento Automático no WhatsApp / IA para o meu negócio.',
        ],
        [
            'id' => 'rpa-automacao',
            'badge' => 'Economia de Tempo • Financeiro & Operação',
            'icon' => 'bi-gear-wide-connected',
            'color' => 'teal',
            'title' => 'Automação de Planilhas, Relatórios e Rotinas (RPA)',
            'problem' => 'Sua equipe gasta horas copiando dados entre planilhas, emitindo relatórios ou preenchendo sistemas?',
            'description' => 'Transformo processos manuais demorados no Excel, ERP ou navegador em robôs automáticos (Python e VBA) que executam tudo em segundos, sem erros humanos.',
            'deliverables' => [
                'Automação avançada de planilhas Excel (VBA) e cruzamento de dados',
                'Robôs em Python para conciliação financeira, RH, estoque e emissão de relatórios',
                'Disparo automático de avisos, cobranças e acompanhamentos',
            ],
            'whatsapp_msg' => 'Olá João! Vi sua página e preciso automatizar planilhas/processos repetitivos na minha empresa.',
        ],
        [
            'id' => 'sistemas-web',
            'badge' => 'Sob Medida • Gestão & Escala',
            'icon' => 'bi-window-stack',
            'color' => 'indigo',
            'title' => 'Sistemas Web, Painéis Gerenciais (BI) e Integrações',
            'problem' => 'Seu sistema atual é travado, lento ou não conversa com as outras ferramentas da empresa?',
            'description' => 'Desenvolvimento e evolução de sistemas de gestão (CRM/ERP), controle de agenda, dashboards de indicadores (KPIs) e integração entre plataformas via API.',
            'deliverables' => [
                'Sistemas web completos e seguros em PHP (Laravel), Java ou React',
                'Painéis gerenciais claros para acompanhar vendas, agenda e produtividade',
                'Integração entre site, sistema interno, banco de dados e telefonia',
            ],
            'whatsapp_msg' => 'Olá João! Vi sua página e gostaria de um orçamento para desenvolvimento/evolução de sistema web ou integração.',
        ],
        [
            'id' => 'landing-pages',
            'badge' => 'Entrega Rápida • Vendas Fim de Ano',
            'icon' => 'bi-rocket-takeoff',
            'color' => 'amber',
            'title' => 'Landing Pages Rápidas & Páginas de Vendas',
            'problem' => 'Precisa divulgar um serviço ou campanha de fim de ano com uma página profissional que converte?',
            'description' => 'Criação de páginas modernas, ultra-rápidas no celular e otimizadas para transformar visitantes do Google e Instagram em chamadas diretas no seu WhatsApp.',
            'deliverables' => [
                'Design limpo, amigável e 100% pensado para celular (Mobile-First)',
                'Botões estratégicos de conversão direta para o WhatsApp e QR Code próprio',
                'Pronta para campanhas no Google Ads, Meta Ads e cartão digital',
            ],
            'whatsapp_msg' => 'Olá João! Vi sua página e quero fazer um orçamento para uma Landing Page profissional para o meu negócio.',
        ],
    ],

    'business_steps' => [
        [
            'step' => '01',
            'title' => 'Diagnóstico Rápido e Gratuito',
            'desc' => 'Você me chama no WhatsApp, conta qual é o gargalo do seu negócio (atendimento, planilha ou site) e avaliamos a solução mais simples e econômica.',
        ],
        [
            'step' => '02',
            'title' => 'Proposta Clara & Prazo Fechado',
            'desc' => 'Sem termos técnicos complicados: você recebe o escopo exato, valor justo e data de entrega definida antes de começarmos.',
        ],
        [
            'step' => '03',
            'title' => 'Entrega Prática + Suporte Próximo',
            'desc' => 'Desenvolvo, testo com você e entrego funcionando, com suporte direto em Santo André/ABC ou remoto.',
        ],
    ],

    'tech_skills' => [
        [
            'category' => 'Backend & Arquitetura',
            'icon' => 'bi-hdd-network',
            'items' => ['PHP 8+ (Laravel / Eloquent ORM)', 'Java 21 (Spring Boot)', 'Spring Data JPA / Hibernate', 'Python', 'Node.js', 'RESTful APIs & OpenAPI/Swagger', 'Autenticação Stateless (Bearer / BCrypt)'],
        ],
        [
            'category' => 'Bancos de Dados & Performance',
            'icon' => 'bi-database-check',
            'items' => ['MySQL (Multi-Database & Schemas)', 'Oracle Database', 'Modelagem Relacional (1:N)', 'Processamento em Lotes (Chunks)', 'Dirty Checking & Memory Tuning', 'Migrations Seguras & Query Tuning'],
        ],
        [
            'category' => 'IA, Voz, Mensageria & CRM',
            'icon' => 'bi-cpu',
            'items' => ['OpenAI API', 'Vapi & LiveKit (Voice AI Agents)', 'WhatsApp Business API (Dialog360/Meta)', 'RCS / SMS (Unipix)', 'Discadores Automáticos (Olos / Voll)', 'Webhooks Dinâmicos & Rotinas de Fallback'],
        ],
        [
            'category' => 'Frontend, Qualidade & DevOps',
            'icon' => 'bi-code-square',
            'items' => ['React.js (React 19) & Vite', 'JavaScript (ES6+) & jQuery', 'HTML5, CSS3 & Bootstrap 5', 'JUnit 5 & Mockito', 'AWS S3 & Linux', 'Git/GitHub, Maven & Laravel Artisan/Cron'],
        ],
    ],

    'experiences' => [
        [
            'company' => 'Pés Sem Dor',
            'role' => 'Desenvolvedor Full-Stack (PHP / Laravel)',
            'period' => 'julho de 2026 – novembro de 2026',
            'location' => 'São Paulo / SP',
            'highlights' => [
                'Arquitetura & Sistemas Corporativos: Desenvolvimento e manutenção evolutiva do ERP/CRM proprietário e da plataforma de avaliação clínica em arquitetura multi-database utilizando PHP (Laravel), MySQL, JavaScript/jQuery e Bootstrap.',
                'Integrações com IA & Agentes de Voz: Construção de integrações via Webhooks e APIs RESTful com plataformas de voz baseadas em IA (Vapi, LiveKit) e OpenAI API, estruturando tratamento de payloads dinâmicos, rotinas de fallback e relatórios analíticos de chamadas.',
                'Automação de Mensageria & CRM: Implementação de integrações com WhatsApp Business API (Dialog360/Meta) e webservices RCS/SMS (Unipix), criando rotinas agendadas (Laravel Artisan/Cron) para sincronização de templates HSM e geração automatizada de leads.',
                'Integração de Discadores & Pipelines de Vendas: Evolução do pipeline de distribuição de leads integrando Chatbot, CRM e discadores automáticos (Olos/Voll), aplicando modelagem relacional (1:N) e filtros avançados.',
                'Otimização & Performance de Dados: Redução de consumo de memória e operações de escrita em banco por meio do processamento de grandes volumes em lotes (chunks), aplicação de dirty checking e migrations seguras.',
                'Dashboards, BI & Módulos Operacionais: Gestão de disponibilidade de agenda integrada ao AWS S3, relatórios gerenciais/KPIs automatizados e evolução do módulo clínico de vetorização/desenho de palmilhas.',
            ],
            'tags' => ['PHP', 'Laravel', 'MySQL Multi-DB', 'Vapi', 'LiveKit', 'OpenAI API', 'WhatsApp API', 'AWS S3', 'Olos/Voll'],
        ],
        [
            'company' => 'KOSTAL Brasil',
            'role' => 'Assistente SAP SD / Desenvolvedor de Soluções & RPA',
            'period' => 'Outubro de 2023 – Março de 2025',
            'location' => 'São Bernardo do Campo / SP',
            'highlights' => [
                'Engenharia de Soluções & RPA em Ambiente Restrito: Projetei e implementei automações de processos (RPA) para as divisões de Financeiro, Controladoria, RH, Qualidade e Produção em Python, VBA e infraestrutura nativa sob normas globais de compliance de TI.',
                'Rollout Internacional SAP PM: Colaborei no projeto de rollout global do módulo SAP PM junto à matriz na Alemanha (em inglês), atuando no levantamento de requisitos técnicos, migração e validação de dados de ativos industriais.',
            ],
            'tags' => ['Python', 'VBA', 'RPA', 'SAP SD / PM', 'Compliance TI', 'Rollout Internacional (EN)'],
        ],
        [
            'company' => 'KOSTAL Brasil',
            'role' => 'Estagiário de TI',
            'period' => 'Fevereiro de 2023 – Outubro de 2023',
            'location' => 'São Bernardo do Campo / SP',
            'highlights' => [
                'Suporte Técnico & Operações: Atendimento corporativo via Service Desk, monitoramento de operações com EDI (Electronic Data Interchange) e suporte funcional ao ecossistema SAP.',
            ],
            'tags' => ['Service Desk', 'EDI', 'SAP'],
        ],
    ],

    'featured_projects' => [
        [
            'title' => 'Fintech Opportunity (The Opportunity Ledger)',
            'subtitle' => 'Plataforma Full-Stack & IA Preditiva de Saúde Financeira',
            'repo_display' => 'github.com/joaooliveiradev2505/FintechFiap',
            'repo_url' => 'https://github.com/joaooliveiradev2505/FintechFiap',
            'bullets' => [
                'Arquitetura Backend & Persistência Oracle: API RESTful em Java 21 e Spring Boot (26 endpoints documentados via Swagger/OpenAPI) integrada ao Oracle Database via Spring Data JPA/Hibernate, com autenticação stateless, BCrypt e controle transacional.',
                'Motor de Custo de Oportunidade & IA (Java + Python): Algoritmos financeiros de Orçamento Reverso, projeção de juros compostos em tempo real e consultor preditivo de saúde financeira em Python, validados com JUnit 5 e Mockito.',
                'Frontend Responsivo (React 19 + Bootstrap 5): SPA responsiva (Mobile/Desktop) com componentes interativos drag-and-drop e assistente virtual integrado.',
            ],
            'stack' => ['Java 21', 'Spring Boot', 'Oracle Database', 'Spring Data JPA', 'Python (IA)', 'React 19', 'Bootstrap 5', 'JUnit 5 & Mockito', 'Swagger/OpenAPI'],
        ],
    ],

    'education' => [
        [
            'degree' => 'Tecnólogo (CST) em Análise e Desenvolvimento de Sistemas',
            'institution' => 'FIAP',
            'period' => '2026 – 2028 (Cursando)',
            'details' => 'Foco em arquitetura de sistemas modernos, Java Spring Boot, banco de dados Oracle, React e Inteligência Artificial.',
        ],
        [
            'degree' => 'Bacharelado em Engenharia de Computação (4 anos cursados)',
            'institution' => 'Universidade Anhembi Morumbi / Fundação Santo André',
            'period' => '2021 – 2025',
            'details' => 'Sólida formação analítica em algoritmos, estruturas de dados, engenharia de software e sistemas computacionais.',
        ],
    ],

    'languages' => [
        ['lang' => 'Português', 'level' => 'Nativo'],
        ['lang' => 'Inglês', 'level' => 'Avançado / Fluente (Comunicação técnica e condução de projetos com times internacionais)'],
    ],
];
