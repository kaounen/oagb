<?php
// admin/includes/partials/gallery_form.php
// Expected: $type ('noticia'|'evento'), $entity_id, $gallery (array)
?>
<div class="card bg-white border mt-4">
    <div class="card-header bg-dark text-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-uppercase small"><i class="fas fa-images me-2 text-login"></i> Galeria de Imagens</h6>
        <span class="badge bg-login-subtle text-login">Slider Automático</span>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <label class="form-label fw-bold small text-muted">ADICIONAR NOVAS FOTOS</label>
            <input type="file" name="gallery_files[]" id="gallery_files_input" class="form-control bg-light border-0" multiple accept="image/*" onchange="previewGalleryImages(this)">
            <div class="x-small text-muted mt-1 italic">Pode selecionar múltiplas fotos de uma vez.</div>
                        <!-- Container para Pré-visualização Instantânea -->
            <div id="new-gallery-preview" class="row g-3 mt-3 d-none">
                <div class="col-12"><small class="text-login fw-bold"><i class="fas fa-eye me-1"></i> Pré-visualização das novas fotos:</small></div>
                <!-- Imagens aparecerão aqui via JS -->
            </div>
        </div>

        <script>
        function previewGalleryImages(input) {
            const container = document.getElementById('new-gallery-preview');
            const title = container.querySelector('.col-12');
            container.innerHTML = '';
            container.appendChild(title);
            
            if (input.files && input.files.length > 0) {
                container.classList.remove('d-none');
                Array.from(input.files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const col = document.createElement('div');
                            col.className = 'col-12';
                            col.innerHTML = `
                                <div class="p-3 border rounded bg-white shadow-sm mb-4 position-relative">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 10px; left: 10px; z-index: 20; border-radius: 50%; width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center;" onclick="this.closest('.col-12').remove()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <!-- Imagem Maior no Topo -->
                                    <div class="position-relative ratio ratio-16x9 border rounded overflow-hidden shadow-sm bg-light mb-3 cursor-pointer" onclick="openLightbox('${e.target.result}', this)">
                                        <img src="${e.target.result}" class="object-fit-cover w-100 h-100">
                                        <div class="position-absolute lupa-trigger" style="top: 10px; right: 10px; z-index: 10;">
                                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 28px; height: 28px; border: 1px solid rgba(0,0,0,0.1);">
                                                <i class="fas fa-search-plus transition" style="font-size: 0.8rem; color: #B1A276;"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Campos por baixo -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-1">Título / Legenda</label>
                                        <input type="text" name="new_gal_title[]" class="form-control border-0 bg-light shadow-sm py-2" placeholder="Ex: Foto do evento...">
                                    </div>
                                    <div>
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-1">Descrição Detalhada</label>
                                        <textarea name="new_gal_desc[]" class="form-control border-0 bg-light shadow-sm" rows="2" placeholder="Opcional..."></textarea>
                                    </div>
                                </div>
                            `;
                            container.appendChild(col);
                        }
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                container.classList.add('d-none');
            }
        }
        </script>

        <?php if (!empty($gallery)): ?>
            <div class="row g-4">
                <div class="col-12"><label class="form-label fw-bold small text-muted">FOTOS ATUAIS NA GALERIA</label></div>
                <?php foreach ($gallery as $img): ?>
                    <div class="col-12">
                        <div class="p-3 border rounded bg-white shadow-sm hover-shadow transition mb-3">
                            <!-- Imagem Maior no Topo -->
                            <div class="position-relative border rounded overflow-hidden shadow-sm mb-3 cursor-pointer" style="aspect-ratio: 16/9;" onclick="openLightbox('/oagb/uploads/<?php echo $img['imagem']; ?>', this)">
                                <img src="/oagb/uploads/<?php echo $img['imagem']; ?>" class="w-100 h-100 object-fit-cover">
                                <div class="position-absolute lupa-trigger" style="top: 10px; right: 10px; z-index: 10;">
                                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 28px; height: 28px; border: 1px solid rgba(0,0,0,0.1);">
                                        <i class="fas fa-search-plus transition" style="font-size: 0.8rem; color: #B1A276;"></i>
                                    </div>
                                </div>
                                <a href="javascript:void(0);" class="btn btn-danger btn-sm position-absolute" style="top: 10px; left: 10px; z-index: 5; border-radius: 50%; width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center;" onclick="event.stopPropagation(); deleteMedia(<?php echo $img['id']; ?>, 'gallery_<?php echo $type; ?>', this);">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                            <!-- Campos por baixo -->
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-1">Título / Legenda</label>
                                <input type="text" name="gal_title[<?php echo $img['id']; ?>]" class="form-control border-0 bg-light shadow-sm py-2" placeholder="Ex: Bastonário em conferência..." value="<?php echo htmlspecialchars($img['legenda']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-1">Descrição Detalhada</label>
                                <textarea name="gal_desc[<?php echo $img['id']; ?>]" class="form-control border-0 bg-light shadow-sm" rows="2" placeholder="Texto que aparece no slider do site..."><?php echo htmlspecialchars($img['descricao']); ?></textarea>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                <label class="small fw-bold text-muted">ORDEM NO SLIDER:</label>
                                <input type="number" name="gal_order[<?php echo $img['id']; ?>]" class="form-control border-0 bg-light shadow-sm text-center" style="width: 60px;" value="<?php echo $img['ordem_exibicao']; ?>">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
     </div>
        <?php else: ?>
            <div class="text-center py-4 border rounded dashed">
                <i class="fas fa-images fa-2x text-muted opacity-25 mb-2"></i>
                <div class="small text-muted">Nenhuma foto na galeria deste item.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
