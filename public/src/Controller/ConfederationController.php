<?php

namespace App\Controller;

use Core\MaintenanceController;
use Backend\Service\ConfederationService;
use Core\Helper;

/**
 * @property ConfederationService $service Sobrescreve a propriedade herdada 
 * para análise estática.
 */
class ConfederationController extends MaintenanceController
{
    public function __construct()
    {
        $confederationService = new ConfederationService();
        // Chama o construtor do Controller Base, passando as configurações específicas
        parent::__construct(
            entityName: 'confederacao',
            listView: 'maintenance/manutencao_confederacoes',
            formView: 'maintenance/manutencao_confederacoes_form',
            baseRoute: '/manutencao/confederacoes',
            service: $confederationService,
            titulo: '',
            subTitulo: 'Cadastro das Confederações de Futebol',
            detalhes: '',
        );
    }

    public function save(?int $id = null)
    {
        //var_dump($_SERVER["REQUEST_METHOD"], $id, $_POST);
        //die();
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return $this->handleGetRequest($id);
        }

        $idUpdate = $_POST['id'] ?? $id;
        $sigla = strtoupper(trim($_POST['sigla'] ?? ''));
        $nome_completo = trim($_POST['nome_completo'] ?? '');
        $url_logo = trim($_POST['url_logo'] ?? '');

        // Lógica de Validação...
        if (empty($sigla) || empty($nome_completo)) {
            $this->setSessionMessage('<div class="alert alert-danger" role="alert">A Sigla e o Nome Completo são obrigatórios.</div>');
            return $this->index();
        }
        
        // Define se é criação ou atualização
        if ($id) {
            // 💡 Atualização
            $result = $this->service->update($idUpdate, $sigla, $nome_completo, $url_logo);
            $successMessage = "Confederação {$sigla} atualizada com sucesso!";
        } else {
            // 💡 Criação
            $result = $this->service->createConfederation($sigla, $nome_completo, $url_logo);
            $successMessage = "Confederação {$sigla} cadastrada com sucesso!";
        }
        
        if ($result === true) {
            $this->setSessionMessage('<div class="alert alert-success" role="alert">' . $successMessage . '</div>');
            $this->redirect($this->baseRoute);
        } else {
            // Lidar com erros específicos (ex: Sigla já existe)
            $this->setSessionMessage('<div class="alert alert-danger" role="alert">Falha ao salvar: ' . $result . '</div>');
            return $this->form($id);
        }
    }

    public function exportData()
    {
        // Define o caminho e nome do arquivo
        $exportDir = __DIR__ . '/../../assets/exports/';
        
        // Garante que a pasta existe
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0777, true);
        }
        
        $fileName = 'confederacoes' . '.json';
        $filePath = $exportDir . $fileName;

        $result = $this->service->exportToJson($filePath);

        if ($result === true) {
            $message = '<div class="alert alert-success" role="alert">Exportação concluída! Arquivo salvo em: <strong>' . htmlspecialchars($fileName) . '</strong></div>';
        } else {
            $message = '<div class="alert alert-danger" role="alert">Falha na exportação: ' . htmlspecialchars($result) . '</div>';
        }

        $this->setSessionMessage($message);
        $this->redirect($this->baseRoute); // Redireciona de volta para a listagem
    }

}