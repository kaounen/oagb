<?php
/**
 * Gerador de Papel Timbrado - Lirio dos Vales
 * Gera um documento Word (.docx) com papel timbrado profissional
 * 
 * Uso: Executar no navegador ou via CLI
 * php gerar-papel-timbrado.php
 */

// Nome do ficheiro de saída
$output_file = __DIR__ . '/papel-timbrado-lirio-dos-vales.docx';

// Caminho para o logo
$logo_path = __DIR__ . '/img/logo_lirio_dos_vales.png';

if (!file_exists($logo_path)) {
    die("Erro: Logo não encontrado em $logo_path\n");
}

// Ler e codificar a imagem
$logo_data = base64_encode(file_get_contents($logo_path));
$logo_size = getimagesize($logo_path);

// Conteúdo do documento XML para Word (formato Office Open XML)
$word_content = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
            xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"
            xmlns:v="urn:schemas-microsoft-com:vml"
            xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing">

<w:body>
    <!-- Cabeçalho -->
    <w:sectPr>
        <w:hdr w:type="odd">
            <w:p>
                <w:pPr>
                    <w:pBdr>
                        <w:bottom w:val="single" w:sz="12" w:space="1" w:color="2E7D32"/>
                    </w:pBdr>
                    <w:spacing w:after="0"/>
                </w:pPr>
                <w:r>
                    <w:rPr>
                        <w:noProof/>
                    </w:rPr>
                    <w:drawing>
                        <wp:inline distT="0" distB="0" distL="0" distR="0">
                            <wp:extent cx="2000000" cy="800000"/>
                            <wp:effectExtent l="0" t="0" r="0" b="0"/>
                            <wp:docPr id="1" name="Logo"/>
                            <a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">
                                <a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">
                                    <pic:picture xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">
                                        <pic:nvPicPr>
                                            <pic:cNvPr id="1" name="Logo Lirio dos Vales"/>
                                            <pic:cNvPicPr/>
                                        </pic:nvPicPr>
                                        <pic:blipFill>
                                            <a:blip r:embed="rId1"/>
                                            <a:stretch>
                                                <a:fillRect/>
                                            </a:stretch>
                                        </pic:blipFill>
                                        <pic:spPr>
                                            <a:xfrm>
                                                <a:off x="0" y="0"/>
                                                <a:ext cx="2000000" cy="800000"/>
                                            </a:xfrm>
                                            <a:prstGeom prst="rect">
                                                <a:avLst/>
                                            </a:prstGeom>
                                        </pic:spPr>
                                    </pic:picture>
                                </a:graphicData>
                            </a:graphic>
                        </wp:inline>
                    </w:drawing>
                </w:r>
            </w:p>
        </w:hdr>

        <!-- Rodapé -->
        <w:ftr w:type="odd">
            <w:p>
                <w:pPr>
                    <w:pBdr>
                        <w:top w:val="single" w:sz="12" w:space="1" w:color="2E7D32"/>
                    </w:pBdr>
                    <w:jc w:val="center"/>
                    <w:spacing w:before="0"/>
                </w:pPr>
                <w:r>
                    <w:rPr>
                        <w:sz w:val="16"/>
                        <w:color w:val="2E7D32"/>
                        <w:fontFamily w:ascii="Arial" w:hAnsi="Arial"/>
                    </w:rPr>
                    <w:t>Lírio dos Vales | Contato: (00) 00000-0000 | email@liriodosvales.com</w:t>
                </w:r>
            </w:p>
        </w:ftr>

        <!-- Margens da página -->
        <w:pgSz w:w="12240" w:h="15840"/>
        <w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="720" w:footer="720" w:gutter="0"/>
    </w:sectPr>

    <!-- Corpo do documento - Área editável -->
    <w:p>
        <w:pPr>
            <w:spacing w:before="480" w:after="240"/>
        </w:pPr>
        <w:r>
            <w:rPr>
                <w:sz w:val="24"/>
                <w:b/>
                <w:color w:val="333333"/>
                <w:fontFamily w:ascii="Arial" w:hAnsi="Arial"/>
            </w:rPr>
            <w:t>Título do Documento</w:t>
        </w:r>
    </w:p>

    <w:p>
        <w:pPr>
            <w:spacing w:before="120" w:after="120"/>
        </w:pPr>
        <w:r>
            <w:rPr>
                <w:sz w:val="22"/>
                <w:color w:val="666666"/>
                <w:fontFamily w:ascii="Arial" w:hAnsi="Arial"/>
            </w:rPr>
            <w:t xml:space="preserve">Data: _____/_____/__________</w:t>
        </w:r>
    </w:p>

    <w:p>
        <w:pPr>
            <w:spacing w:before="240" w:after="120"/>
        </w:pPr>
        <w:r>
            <w:rPr>
                <w:sz w:val="22"/>
                <w:color w:val="333333"/>
                <w:fontFamily w:ascii="Arial" w:hAnsi="Arial"/>
            </w:rPr>
            <w:t xml:space="preserve">

</w:t>
        </w:r>
    </w:p>

    <w:p>
        <w:r>
            <w:rPr>
                <w:sz w:val="22"/>
                <w:color w:val="333333"/>
                <w:fontFamily w:ascii="Arial" w:hAnsi="Arial"/>
            </w:rPr>
            <w:t>[Escreva aqui o conteúdo do seu documento...]</w:t>
        </w:r>
    </w:p>

    <w:p>
        <w:pPr>
            <w:spacing w:before="480" w:after="120"/>
        </w:pPr>
        <w:r>
            <w:rPr>
                <w:sz w:val="22"/>
                <w:color w:val="666666"/>
                <w:fontFamily w:ascii="Arial" w:hAnsi="Arial"/>
            </w:rPr>
            <w:t xml:space="preserve">

Atenciosamente,</w:t>
        </w:r>
    </w:p>

    <w:p>
        <w:pPr>
            <w:spacing w:before="480" w:after="120"/>
        </w:pPr>
        <w:r>
            <w:rPr>
                <w:sz w:val="22"/>
                <w:b/>
                <w:color w:val="2E7D32"/>
                <w:fontFamily w:ascii="Arial" w:hAnsi="Arial"/>
            </w:rPr>
            <w:t>Lírio dos Vales</w:t>
        </w:r>
    </w:p>

</w:body>
</w:document>
XML;

// Criar ficheiro Word usando o formato ZIP (DOCX é um ZIP)
$zip = new ZipArchive();
$filename = $output_file;

if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
    die("Não foi possível criar o ficheiro: $filename\n");
}

// [Content Types].xml
$content_types = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Default Extension="png" ContentType="image/png"/>
    <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
</Types>';
$zip->addFromString('[Content_Types].xml', $content_types);

// _rels/.rels
$rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>';
$zip->addFromString('_rels/.rels', $rels);

// word/_rels/document.xml.rels
$word_rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/logo.png"/>
</Relationships>';
$zip->addFromString('word/_rels/document.xml.rels', $word_rels);

// word/document.xml
$zip->addFromString('word/document.xml', $word_content);

// word/media/logo.png
$zip->addFromString('word/media/logo.png', file_get_contents($logo_path));

$zip->close();

echo "✅ Papel timbrado gerado com sucesso!\n";
echo "📄 Ficheiro: $output_file\n";
echo "📐 Tamanho: " . round(filesize($output_file) / 1024, 2) . " KB\n";
echo "\nAbra o ficheiro no Microsoft Word para editar.\n";
