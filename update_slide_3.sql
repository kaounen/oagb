-- Atualizar terceiro slide do carousel
-- Primeiro, vamos ver os slides atuais
SELECT id, titulo, imagem, ordem FROM carousel_slides ORDER BY ordem ASC;

-- Atualizar o terceiro slide (assumindo que é ordem = 3 ou o terceiro por ordem)
UPDATE carousel_slides 
SET imagem = 'close-up-scales-justice-original-azul.jpg' 
WHERE id = (
    SELECT id FROM (
        SELECT id, ROW_NUMBER() OVER (ORDER BY ordem ASC) as rn 
        FROM carousel_slides
    ) ranked 
    WHERE rn = 3
);

-- Verificar se a atualização foi bem-sucedida
SELECT id, titulo, imagem, ordem FROM carousel_slides ORDER BY ordem ASC;