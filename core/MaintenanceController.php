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
    protected string $titulo;
    protected string $subTitulo;
    protected string $detalhes;
    

    public function __construct(string $entityName, string $listView, string $formView, string $baseRoute,
                                string $titulo, string $subTitulo, string $detalhes, MaintenanceService $service)
    {
        parent::__construct();
        $this->entityName = $entityName;
        $this->listViewName = $listView;
        $this->formViewName = $formView;
        $this->baseRoute = $baseRoute;
        $this->service = $service; 
        $this->titulo = $titulo;
        $this->subTitulo = $subTitulo;
        $this->detalhes = $detalhes;
    }

    /**
     * Lida com requisições GET, direcionando para a listagem ou formulário de edição.
     * Este método é chamado pelo save() quando a requisição NÃO é POST.
     * * @param int|null $id O ID da entidade, se presente na URI.
     * @return mixed O resultado da chamada a index() ou form().
     */
    protected function handleGetRequest(?int $id = null)
    {
        if ($id === null) {
            // Rota estática /manutencao/entidade (GET) -> Vai para a listagem
            return $this->index(); 
        } else {
            // Rota dinâmica /manutencao/entidade/editar/{id} (GET) -> Vai para o formulário de edição
            return $this->form($id); 
        }
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
        // var_dump($entities);
        // die();

        
        // Define o título da página
        $pageTitle = '';
        $pageSubtitle = '';
        $pageDetail = '';
        if (!empty($this->titulo)) {
            $pageTitle = $this->titulo;
        } else {
            $pageTitle = 'Manutenção de ' . $this->formatNameEntity($this->entityName);
        }
        if (!empty($this->subTitulo)) {
            $pageSubtitle = $this->subTitulo;
        }
        if (!empty($this->detalhes)) {
            $pageDetail = $this->detalhes;
        }
        
        // Renderiza a View de Listagem
        $this->render($this->listViewName, [
            'pageTitle' => $pageTitle,
            'pageSubtitle' => $pageSubtitle,
            'pageDetail' => $pageDetail,
            'message' => $message,
            'data' => $entities,
            'baseRoute' => $this->baseRoute,
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
            'isEditing' => $isEditing,
            'baseRoute' => $this->baseRoute
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