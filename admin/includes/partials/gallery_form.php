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
            <input type="file" name="gallery_files[]" class="form-control bg-light border-0" multiple accept="image/*">
            <div class="x-small text-muted mt-1 italic">Pode selecionar múltiplas fotos de uma vez.</div>
        </div>

        <?php if (!empty($gallery)): ?>
            <div class="row g-3">
                <?php foreach ($gallery as $img): ?>
                    <div class="col-12">
                        <div class="p-3 border rounded bg-light d-flex gap-3 align-items-start">
                            <div class="position-relative" style="width: 100px; height: 100px; flex-shrink: 0;">
                                <img src="/oagb/uploads/<?php echo $img['imagem']; ?>" class="rounded w-100 h-100 object-fit-cover border shadow-sm">
                                <a href="?delete_gallery=<?php echo $img['id']; ?>&id=<?php echo $entity_id; ?>" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-1 rounded-circle shadow" onclick="return confirm('Apagar esta foto da galeria?');">
                                    <i class="fas fa-times" style="font-size: 0.7rem;"></i>
                                </a>
                            </div>
                            <div class="flex-grow-1">
                                <input type="text" name="gal_title[<?php echo $img['id']; ?>]" class="form-control form-control-sm mb-2 border-0 shadow-sm" placeholder="Título/Legenda curta" value="<?php echo htmlspecialchars($img['legenda']); ?>">
                                <textarea name="gal_desc[<?php echo $img['id']; ?>]" class="form-control form-control-sm border-0 shadow-sm" rows="2" placeholder="Descrição detalhada para o slider"><?php echo htmlspecialchars($img['descricao']); ?></textarea>
                                <div class="d-flex align-items-center mt-2">
                                    <label class="x-small text-muted me-2">Ordem:</label>
                                    <input type="number" name="gal_order[<?php echo $img['id']; ?>]" class="form-control form-control-sm border-0 shadow-sm" style="width: 60px;" value="<?php echo $img['ordem_exibicao']; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-4 border rounded dashed">
                <i class="fas fa-images fa-2x text-muted opacity-25 mb-2"></i>
                <div class="small text-muted">Nenhuma foto na galeria deste item.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
