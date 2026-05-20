            </div> <!-- /content -->
        </div> <!-- /page-content-wrapper -->
    </div> <!-- /wrapper -->

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleButton = document.getElementById("sidebarToggle");
            const sidebar = document.getElementById("sidebar-wrapper");
            
            if (toggleButton) {
                toggleButton.addEventListener("click", function(e) {
                    e.preventDefault();
                    sidebar.classList.toggle("show");
                });
            }
            
            // Auto-close sidebar on mobile when clicking outside
            document.addEventListener("click", function(e) {
                if (window.innerWidth < 992 && 
                    !sidebar.contains(e.target) && 
                    !toggleButton.contains(e.target) && 
                    sidebar.classList.contains("show")) {
                    sidebar.classList.remove("show");
                }
            });
        });
        function deleteMedia(id, type, btn) {
            if (!confirm('Tem a certeza que deseja eliminar este ficheiro permanentemente?')) return;
            
            // Tentar encontrar o contentor mais abrangente (coluna ou cartão)
            const card = btn.closest('.col-12') || btn.closest('.shadow-sm') || btn.closest('.p-3') || btn.closest('.position-relative');
            
            if (card) {
                card.style.opacity = '0.5';
                card.style.pointerEvents = 'none';
            }

            const formData = new FormData();
            formData.append('id', id);
            formData.append('type', type);

            let basePath = '/admin/';
            if (window.location.pathname.includes('/sga/admin/')) {
                basePath = window.location.pathname.substring(0, window.location.pathname.indexOf('/sga/admin/') + 11);
            } else if (window.location.pathname.includes('/admin/')) {
                basePath = window.location.pathname.substring(0, window.location.pathname.indexOf('/admin/') + 7);
            }
            fetch(basePath + 'includes/ajax_media_delete.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Se for foto de destaque ou PDF principal, NÃO remover o card pai (para manter o campo de upload)
                    if (type.includes('highlight') || type.includes('quick_pdf')) {
                        const preview = document.getElementById('preview');
                        if (preview && type.includes('highlight')) {
                            preview.src = '';
                            preview.classList.add('d-none');
                        }
                        
                        // Remover apenas o bloco de pré-visualização (o mais perto com .position-relative)
                        const previewBlock = btn.closest('.position-relative');
                        if (previewBlock && !type.includes('highlight')) {
                            previewBlock.remove();
                        } else if (previewBlock) {
                            // Se for destaque, apenas esconder o botão e limpar
                            btn.style.display = 'none';
                        }

                        if (card && (type.includes('highlight') || type.includes('quick_pdf'))) {
                            card.style.opacity = '1';
                            card.style.pointerEvents = 'auto';
                        }
                    } else {
                        // Para outros tipos (galeria/anexos), remover o contentor completo
                        if (card) {
                            card.style.transform = 'scale(0.8)';
                            card.style.transition = '0.3s';
                            setTimeout(() => card.remove(), 300);
                        }
                    }
                } else {
                    alert('Erro ao eliminar: ' + (data.message || 'Erro desconhecido'));
                    if (card) {
                        card.style.opacity = '1';
                        card.style.pointerEvents = 'auto';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erro de conexão ao servidor.');
                card.style.opacity = '1';
                card.style.pointerEvents = 'auto';
            });
        }

        function openLightbox(src, triggerEl) {
            const modalHtml = `
                <div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl">
                        <div class="modal-content bg-transparent border-0">
                            <div class="modal-body p-0 text-center position-relative">
                                <div class="d-inline-block position-relative shadow-lg rounded">
                                    <!-- Lupa Marrom no Popup -->
                                    <div class="position-absolute" style="top: 20px; left: 20px; z-index: 1060;">
                                        <i class="fas fa-search-plus" style="color: #B1A276; font-size: 1.5rem; filter: drop-shadow(0px 0px 5px rgba(0,0,0,0.5));"></i>
                                    </div>
                                    
                                    <!-- Botão Fechar Marrom -->
                                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 shadow" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1060; background-color: #B1A276; padding: 15px; border-radius: 50%; opacity: 1; border: 2px solid #fff;"></button>
                                    
                                    <img src="${src}" class="img-fluid rounded" style="max-height: 90vh;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remover modal anterior se existir
            const oldModal = document.getElementById('lightboxModal');
            if (oldModal) {
                const bsModal = bootstrap.Modal.getInstance(oldModal);
                if (bsModal) bsModal.dispose();
                oldModal.remove();
            }
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modalEl = document.getElementById('lightboxModal');
            const myModal = new bootstrap.Modal(modalEl);
            
            myModal.show();
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Adicionar funcionalidade de zoom a todos os elementos com ID #preview
            document.querySelectorAll('img#preview').forEach(img => {
                img.style.cursor = 'pointer';
                img.title = 'Clique para ampliar';
                img.addEventListener('click', function() {
                    if (this.src && !this.src.includes('placeholder')) {
                        openLightbox(this.src, this);
                    }
                });
            });
        });
    </script>
</body>
</html>
