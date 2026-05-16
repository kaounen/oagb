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
            <input type="file" name="attachments[]" id="attachments_input" class="form-control" multiple onchange="previewAttachments(this)">
            
            <div id="attachments-preview" class="mt-3 d-none">
                <small class="text-muted fw-bold d-block mb-2">Ficheiros selecionados para carregar:</small>
                <div class="d-flex flex-wrap gap-2" id="attachments-preview-list"></div>
            </div>
        </div>

        <script>
        function previewAttachments(input) {
            const container = document.getElementById('attachments-preview');
            const list = document.getElementById('attachments-preview-list');
            list.innerHTML = '';
            
            if (input.files && input.files.length > 0) {
                container.classList.remove('d-none');
                Array.from(input.files).forEach(file => {
                    const item = document.createElement('div');
                    item.className = 'w-100 bg-light border rounded p-3 mb-2';
                    
                    let icon = '<i class="far fa-file-alt fa-lg text-muted"></i>';
                    if (file.type.includes('pdf')) icon = '<i class="far fa-file-pdf fa-lg text-danger"></i>';
                    if (file.type.includes('image')) icon = '<i class="far fa-file-image fa-lg text-primary"></i>';
                    
                    item.innerHTML = `
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">${icon}</div>
                            <div class="col text-truncate small fw-bold">${file.name}</div>
                            <div class="col-12 col-md-8">
                                <input type="text" name="attachment_descriptions[]" class="form-control form-control-sm border-0 shadow-sm" placeholder="Título ou legenda do ficheiro...">
                            </div>
                        </div>
                    `;
                    list.appendChild(item);
                });
            } else {
                container.classList.add('d-none');
            }
        }
        </script>

        <?php if (!empty($attachments)): ?>
            <div class="mt-4">
                <label class="form-label small fw-bold text-muted text-uppercase">Ficheiros já carregados:</label>
                <?php foreach ($attachments as $att): ?>
                    <div class="p-3 border rounded bg-white shadow-sm mb-3">
                        <div class="d-flex align-items-start mb-2">
                            <div class="me-3">
                                <?php if(strpos($att['tipo_mime'], 'image') !== false): ?>
                                    <img src="/oagb/uploads/attachments/<?php echo $att['nome_ficheiro']; ?>" class="rounded border shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded text-center border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <?php if(strpos($att['tipo_mime'], 'pdf') !== false): ?>
                                            <i class="far fa-file-pdf text-danger fa-lg"></i>
                                        <?php else: ?>
                                            <i class="far fa-file-alt text-muted fa-lg"></i>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1 min-width-0" style="overflow: hidden;">
                                <a href="/oagb/uploads/attachments/<?php echo $att['nome_ficheiro']; ?>" target="_blank" class="text-decoration-none d-block">
                                    <div class="small fw-bold text-truncate text-primary" title="<?php echo htmlspecialchars($att['nome_original']); ?>" style="max-width: 100%;">
                                        <?php echo htmlspecialchars($att['nome_original']); ?>
                                    </div>
                                    <div class="x-small text-muted"><?php echo number_format($att['tamanho'] / 1024, 1); ?> KB</div>
                                </a>
                            </div>
                            <div class="flex-shrink-0 ms-2">
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger border-0" onclick="deleteMedia(<?php echo $att['id']; ?>, 'attachment', this);">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                        <div>
                            <label class="x-small fw-bold text-muted text-uppercase mb-1 d-block">Título / Descrição no site:</label>
                            <input type="text" name="att_desc[<?php echo $att['id']; ?>]" class="form-control form-control-sm border-0 bg-light" placeholder="Sem título..." value="<?php echo htmlspecialchars($att['descricao']); ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
