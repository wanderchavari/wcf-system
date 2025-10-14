<?php
// O $ambiente e $versao virão do Controller
// O $titulo deve vir do Controller
?>
<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>WCF System - <?= $titulo ?? 'Home' ?></title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href='/assets/css/style.css' rel='stylesheet'>
</head>

<body class="d-flex flex-column min-vh-100 text-light">

<nav class='navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary'>
    <div class='container'>
        <a class='navbar-brand' href='/'>WCF System</a>
        <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarNav' aria-controls='navbarNav' aria-expanded='false' aria-label='Toggle navigation'>
            <span class='navbar-toggler-icon'></span>
        </button>
        <div class='collapse navbar-collapse' id='navbarNav'>
            <ul class='navbar-nav ms-auto'>
                <li class='nav-item'><a class='nav-link active' href='/'>Home</a></li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="javascript:void(0)" 
                    id="navbarDropdownCopas" 
                    role="button" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                        Copas do Mundo
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdownCopas">
                        
                        <li><h6 class="dropdown-header">Detalhes por Edição</h6></li>
                        
                        <?php 
                        if (!empty($torneiosParaMenu) && is_array($torneiosParaMenu)):
                            foreach ($torneiosParaMenu as $torneio): 
                        ?>
                                <li>
                                    <a class="dropdown-item" href="/copas/<?= $torneio['ano_torneio'] ?>">
                                        <?= $torneio['sede'] ?> - <?= $torneio['ano_torneio'] ?>
                                    </a>
                                </li>
                        <?php 
                            endforeach;
                        endif; 
                        ?>
                        
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/copas">Ver todas</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="javascript:void(0)" 
                    id="navbarDropdownRanking" 
                    role="button" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                        Ranking Histórico
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdownRanking">
                        
                        <li><h6 class="dropdown-header">Visão Consolidada</h6></li>
                        <li><a class="dropdown-item" href="/ranking">
                            Geral
                        </a></li>
                        
                        <li><hr class="dropdown-divider"></li>
                        
                        <li><h6 class="dropdown-header">Por Período</h6></li>
                        
                        <li><a class="dropdown-item" href="/ranking?listagem=old">
                            Antigo (até 1990)
                        </a></li>
                        
                        <li><a class="dropdown-item" href="/ranking?listagem=modern">
                            Moderno (desde 1994)
                        </a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/estatisticas">Estatísticas</a>
                </li>

                <!-- <li class='nav-item'><a class='nav-link' href='/admin'>Backend / Admin</a></li> -->
            </ul>
        </div>
    </div>
</nav>

<main class="flex-shrink-0">

    <div class="pt-5 pb-3"> 
        <div class='container'>
            <h1 class='display-4 text-light'>
                <img src="/assets/img/fifa.png" alt="FIFA Logo" style="height: 120px; vertical-align: middle; margin-right: 10px;">
                <?= $pageTitle ?? 'Bem-vindo ao WCF System' ?>
            </h1>
            
            <p class='lead <?= $pageSubtitleClass ?? '' ?>'><?= $pageSubtitle ?? 'Acompanhe o histórico de campeões e torneios.' ?></p>

            <?php if (!empty($pageDetail)): ?>
                <p class='text-light small'><?= $pageDetail ?></p>
            <?php endif; ?>
            
            <hr class='my-4 border-secondary'>
        </div>
    </div>

    <div class="container mb-5"> 

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-5 g-4 justify-content-center">
    
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Seleciona os elementos pai dos dropdowns para gerenciar o estado 'active'
        const copasNavItem = document.getElementById('navbarDropdownCopas').closest('.nav-item');
        const rankingNavItem = document.getElementById('navbarDropdownRanking').closest('.nav-item');
        
        // Seleciona o botão que dispara o dropdown
        const copasToggle = document.getElementById('navbarDropdownCopas');
        const rankingToggle = document.getElementById('navbarDropdownRanking');
        
        // =================================================================
        // 1. LIMPEZA DE ESTADO: Remove 'active' do pai quando o dropdown fecha
        // =================================================================
        function cleanActiveState(event) {
            // Remove a classe 'active' do item pai (nav-item)
            event.target.closest('.nav-item').classList.remove('active');
        }

        if (copasNavItem && rankingNavItem) {
            // Remove a classe 'active' de Copas do Mundo quando ele é escondido
            copasNavItem.addEventListener('hide.bs.dropdown', cleanActiveState);
            
            // Remove a classe 'active' de Ranking Histórico quando ele é escondido
            rankingNavItem.addEventListener('hide.bs.dropdown', cleanActiveState);
        }

        // =================================================================
        // 2. FORÇAR FECHAMENTO E ATIVAÇÃO (Corrige a falha Copas -> Ranking)
        // =================================================================
        if (rankingToggle && copasToggle) {
            
            // Evento para quando o Ranking Histórico está prestes a ABRIR
            rankingToggle.addEventListener('show.bs.dropdown', function () {
                // Força o fechamento de Copas do Mundo
                const bsDropdownCopas = bootstrap.Dropdown.getInstance(copasToggle) || new bootstrap.Dropdown(copasToggle);
                bsDropdownCopas.hide();
                
                // Opcional: Garante que o item pai de Copas perca o 'active' imediatamente
                copasNavItem.classList.remove('active');
            });
            
            // Evento para quando o Copas do Mundo está prestes a ABRIR
            copasToggle.addEventListener('show.bs.dropdown', function () {
                 // Força o fechamento de Ranking Histórico
                const bsDropdownRanking = bootstrap.Dropdown.getInstance(rankingToggle) || new bootstrap.Dropdown(rankingToggle);
                bsDropdownRanking.hide();
                
                // Opcional: Garante que o item pai de Ranking perca o 'active' imediatamente
                rankingNavItem.classList.remove('active');
            });
        }
        
        // =================================================================
        // 3. REMOVER ACTIVE QUANDO UM LINK FILHO É CLICADO
        // (Garante que o pai não fique ativo se o link filho não levar à mesma página)
        // =================================================================
        const allDropdownItems = document.querySelectorAll('.dropdown-menu a.dropdown-item');
        allDropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // Encontra o nav-item pai mais próximo
                const parentNavItem = e.target.closest('.nav-item');
                if (parentNavItem) {
                    // Remove o active imediatamente antes de navegar
                    parentNavItem.classList.remove('active');
                }
            });
        });
    });
</script>