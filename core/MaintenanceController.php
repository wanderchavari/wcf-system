<?php

namespace Core;

use Core\MaintenanceService;
use Core\BaseController; 
use Core\Helper;

/**
 * Classe base abstrata para todos os Controllers de Manutenção (CRUD).
 * Implementa a lógica genérica de Listagem, Busca, Ordenação e Exclusão.
 */
abstract class MaintenanceController extends BaseController
{
    // Propriedades de Configuração (Definidas pelo Controller Filho no __construct)
    protected MaintenanceService $service; // Service específico (Ex: ConfederationService)
    protected string $entityName;        // Ex: 'confederacao' (singular, minúsculo)
    protected string $listViewName;      // Ex: 'maintenance/manutencao_confederacoes'
    protected string $formViewName;      // Ex: 'maintenance/manutencao_confederacoes_form'
    protected string $baseRoute;         // Ex: '/manutencao/confederacoes'
    

    public function __construct(string $entityName, string $listView, string $formView, string $baseRoute, MaintenanceService $service)
    {
        parent::__construct();
        $this->entityName = $entityName;
        $this->listViewName = $listView;
        $this->formViewName = $formView;
        $this->baseRoute = $baseRoute;
        $this->service = $service; 
    }

    // =================================================================
    // 1. LISTAGEM (CRUD: READ)
    // Rota: /manutencao/{entidade}
    // =================================================================
    public function index()
    {
        // 💡 Recupera mensagens de sucesso/erro da sessão (pós-operação)
        $message = $this->getSessionMessage() ?? '';
        
        // Lógica de Ordenação e Busca
        // Usa a chave primária do Service como padrão se nada for definido
        $sort = $_GET['sort'] ?? $this->service->getPrimaryKey(); 
        $dir = $_GET['direction'] ?? 'asc';
        $searchTerm = $_GET['search'] ?? '';

        // Chama o método GENÉRICO do Service para obter os dados
        $entities = $this->service->getAll($sort, $dir, $searchTerm);
        
        // Define o título da página
        $pageTitle = 'Manutenção de ' . $this->formatNameEntity($this->entityName);
        
        // Renderiza a View de Listagem
        $this->render($this->listViewName, [
            'pageTitle' => $pageTitle,
            'message' => $message,
            'data' => $entities,
            'current_sort' => $sort, 
            'current_direction' => $dir,
            'searchTerm' => $searchTerm
        ]);
        
        $this->clearSessionMessage();
    }
    
    // =================================================================
    // 2. EXIBIÇÃO DO FORMULÁRIO (EDIT - GET)
    // Rota: /manutencao/{entidade}/editar/{id}
    // =================================================================
    public function form(?int $id = null)
    {
        $message = $this->getSessionMessage() ?? '';
        $entityData = [];
        $isEditing = false;
        $pageTitle = 'Editando ' . $this->formatNameEntity($this->entityName);
        
        // Se houver ID, estamos em modo de edição
        if ($id) {
            $entityData = $this->service->getById($id);
            
            if (!$entityData) {
                // Trata caso a entidade não seja encontrada (Redireciona para a lista)
                $this->setSessionMessage('<div class="alert alert-danger" role="alert">' . ucfirst($this->entityName) . " com ID {$id} não encontrado.</div>");
                $this->redirect($this->baseRoute);
                return;
            }
            
            $isEditing = true;
            
            // Título de Edição
            // (Usamos o primeiro campo pesquisável como "nome" ou a chave primária)
            $nameField = $this->service->getSearchableFields()[0] ?? $this->entityName;
            $pageTitle = 'Editar ' . $this->formatNameEntity($this->entityName) . ': ' . ($entityData[$nameField] ?? $entityData[$this->service->getPrimaryKey()]);
        }
        
        // Renderiza a View de Formulário
        $this->render($this->formViewName, [
            'pageTitle' => $pageTitle,
            'message' => $message,
            'data' => $entityData, // Ex: 'confederacao' => $entityData
            'isEditing' => $isEditing 
        ]);
        
        $this->clearSessionMessage();
    }

    // =================================================================
    // 3. EXCLUSÃO (CRUD: DELETE)
    // Rota: /manutencao/{entidade}/excluir/{id}
    // =================================================================
    public function delete(int $id)
    {
        $result = $this->service->delete($id); // Chama o método genérico do Service
        
        if ($result === true) {
            $message = '<div class="alert alert-success" role="alert">' . ucfirst($this->entityName) . " ID {$id} excluída com sucesso!</div>";
        } else {
            // Se o Service retornar uma string, é a mensagem de erro (ex: Foreign Key)
            $message = '<div class="alert alert-danger" role="alert">Falha ao excluir ' . $this->entityName . " ID {$id}: {$result}</div>";
        }
        
        // Adiciona mensagem à sessão e redireciona para a listagem
        $this->setSessionMessage($message);
        $this->redirect($this->baseRoute);
    }

    // =================================================================
    // 4. PROCESSAMENTO DO FORMULÁRIO (CREATE/EDIT - POST)
    // ESTE MÉTODO DEVE SER IMPLEMENTADO/SOBRESCRITO PELO FILHO
    // =================================================================
    /**
     * Lógica POST para salvar (CREATE/UPDATE). É obrigatório implementar ou 
     * sobrescrever este método no Controller filho, pois a validação é específica.
     */
    abstract public function save(?int $id = null);

    private function formatNameEntity(string $titleBase) {
        //$titleBase = ucfirst($this->entityName);
        if (str_ends_with($titleBase, 'cao')) {
            return str_replace('cao', 'ções', $titleBase);
        } else if (!str_ends_with($titleBase, 's')) {
            return $titleBase .= 's';
        } elseif (str_ends_with($titleBase, 'coes')) {
            return str_replace('coes', 'ções', $titleBase);
        }
    }
}