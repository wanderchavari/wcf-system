<?php

namespace App\Controller;

use Core\MaintenanceController;
use Backend\Service\TorneioService;

/**
 * @property TorneioService $service Sobrescreve a propriedade herdada 
 * para análise estática.
 */
class TorneioController extends MaintenanceController
{
    public function __construct()
    {
        $torneioService = new TorneioService();
        
        // Chama o construtor do Controller Base, passando as configurações específicas
        parent::__construct(
            entityName: 'torneio', // Nome usado nas views (ex: $torneio['sede'])
            listView: 'maintenance/manutencao_torneios', // View de listagem
            formView: 'maintenance/manutencao_torneios_form', // View de formulário
            baseRoute: '/manutencao/torneios', // Rota base (URI)
            service: $torneioService,
            titulo: '',
            subTitulo: 'Cadastro das Copas do Mundo de Futebol',
            detalhes: '',
        );
    }
    
    // Sobrescreve o método 'save' para incluir a lógica de validação e mapeamento
    public function save(?int $id = null)
    {
        // 💡 Implementação da Lógica de GET (Listar/Form)
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return $this->handleGetRequest($id);
        }

        $ano_torneio = intval($_POST['ano_torneio'] ?? $id);
        $sede = trim($_POST['sede'] ?? '');
        $ponto_por_vitoria = intval($_POST['ponto_por_vitoria'] ?? 0);
        $genero = strtoupper(trim($_POST['genero'] ?? ''));
        $url_cartaz = trim($_POST['url_cartaz'] ?? '');
        $url_mascote = trim($_POST['url_mascote'] ?? '');
        // var_dump($ano_torneio, $sede, $ponto_por_vitoria, $genero, $url_cartaz, $url_mascote);
        // die();
        
        // 1. Lógica de Validação...
        if ($ano_torneio <= 0 || empty($sede) || $ponto_por_vitoria <= 0 || !in_array($genero, ['M', 'F'])) {
            $this->setSessionMessage('<div class="alert alert-danger" role="alert">Dados básicos inválidos. Verifique Ano, Sede, Pontuação e Gênero.</div>');
            return $this->index(); 
        }
        
        // 2. Mapeamento de Dados para o Service
        $data = [
            'ano_torneio' => $ano_torneio,
            'sede' => $sede,
            'ponto_por_vitoria' => $ponto_por_vitoria,
            'genero' => $genero,
            'url_cartaz' => $url_cartaz,
            'url_mascote' => $url_mascote,
        ];

        if ($id) {
            $result = $this->service->update($data);
            $successMessage = "Torneio de {$ano_torneio} atualizado com sucesso!";
        } else {
            // Criação
            $result = $this->service->create($data);
            $successMessage = "Torneio de {$ano_torneio} cadastrado com sucesso!";
        }
        
        // 3. Feedback e Redirecionamento
        if ($result === true) {
            $this->setSessionMessage('<div class="alert alert-success" role="alert">' . $successMessage . '</div>');
            $this->redirect($this->baseRoute);
        } else {
            $this->setSessionMessage('<div class="alert alert-danger" role="alert">Falha ao salvar: ' . $result . '</div>');
            if (!empty($id)) {
                return $this->form($id);
            }
            $this->redirect($this->baseRoute);
        }
    }

    public function exportData()
    {
        $exportDir = __DIR__ . '/../../assets/exports/';
        
        // Garante que a pasta existe
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0777, true);
        }
        
        $fileName = 'torneios' . '.json';
        $filePath = $exportDir . $fileName;

        $result = $this->service->exportToJson($filePath);

        if ($result === true) {
            $message = '<div class="alert alert-success" role="alert">Exportação concluída! Arquivo salvo em: <strong>' . htmlspecialchars($fileName) . '</strong></div>';
        } else {
            $message = '<div class="alert alert-danger" role="alert">Falha na exportação: ' . htmlspecialchars($result) . '</div>';
        }

        $this->setSessionMessage($message);
        $this->redirect($this->baseRoute);
    }

}