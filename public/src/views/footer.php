<?php
// O $ambiente e $versao virão do Controller
?>
    </div> </main> <footer class='footer mt-auto py-3 bg-dark mt-5 border-top border-secondary'>
    <div class='container'>
        <span class='text-light'>
            Ambiente: <strong><?= $ambiente ?></strong> | 
            Versão: <strong><?= $versao ?></strong> | 
            &copy; <?= date('Y') ?> WCF System
        </span>
    </div>
</footer>

<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 💡 Usa o objeto global 'bootstrap'
    if (typeof bootstrap !== 'undefined') {
        var dropdownElement = document.getElementById('navbarDropdownCopas');
        
        if (dropdownElement) {
            // Remove o atributo de auto-inicialização para evitar conflito
            dropdownElement.removeAttribute('data-bs-toggle');
            
            // Inicializa a classe Dropdown manualmente
            var dropdown = new bootstrap.Dropdown(dropdownElement);
            
            // Adiciona um listener de clique ao link
            dropdownElement.addEventListener('click', function(e) {
                e.preventDefault(); 
                dropdown.toggle(); // Alterna a visibilidade do dropdown
            });
        }
    } else {
        console.error("Bootstrap JS não está carregado.");
    }
});
</script>

</body>
</html>