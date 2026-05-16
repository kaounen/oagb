<?php
// admin/includes/partials/attachments_form.php
// Expected variables: $entity_type, $entity_id (0 for add), $attachments (array)
?>
<div class="card bg-white border mt-4">
    <div class="card-header bg-light border-0 py-3">
        <h6 class="mb-0 text-muted text-uppercase small"><i class="fas fa-paperclip me-2"></i> Galeria e Anexos</h6>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label small text-muted">Aperte aqui para carregar vários ficheiros (Fotos, PDFs, etc.):</label>
            <input type="file" name="attachments[]" class="form-control" multiple>
        </div>

        <?php if (!empty($attachments)): ?>
            <div class="table-responsive mt-3">
                <table class="table table-sm table-hover align-middle mb-0 border-top">
                    <thead>
                        <tr class="bg-light">
                            <th class="small py-2">Ficheiro</th>
                            <th class="small py-2 text-center" style="width: 80px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attachments as $att): ?>
                            <tr>
                                <td class="small">
                                    <div class="d-flex align-items-center">
                                        <?php if(strpos($att['tipo_mime'], 'image') !== false): ?>
                                            <img src="/oagb/uploads/attachments/<?php echo $att['nome_ficheiro']; ?>" class="rounded me-2 border" style="width: 32px; height: 32px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded text-center me-2 border" style="width: 32px; height: 32px; line-height:32px;"><i class="far fa-file-alt text-muted"></i></div>
                                        <?php endif; ?>
                                        <div class="text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($att['nome_original']); ?></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="?delete_attachment=<?php echo $att['id']; ?>&id=<?php echo $entity_id; ?>" class="btn btn-sm btn-outline-danger p-1" onclick="return confirm('Eliminar este ficheiro permanentemente?');">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
