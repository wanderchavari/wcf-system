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
                    aria-expanded="false">
                        Copas do Mundo
                    </a>
                    
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownCopas">
                        <li><h6 class="dropdown-header">Escolha o Ano</h6></li>
                        
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

                <li class='nav-item'><a class='nav-link' href='/admin'>Backend / Admin</a></li>
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