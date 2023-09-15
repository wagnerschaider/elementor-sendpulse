# Integração Elementor SendPulse

**Versão:** 1.0

Integre formulários do Elementor com as listas do SendPulse usando este plugin do WordPress.

## Descrição

Este plugin do WordPress permite que você integre perfeitamente formulários do Elementor com o SendPulse, uma plataforma de automação de marketing. Quando os usuários enviam formulários criados com o Elementor em seu site, os dados enviados são automaticamente enviados para a lista de e-mails do SendPulse.

## Instalação

1. Baixe o código-fonte do plugin.
2. Configure a integração com o SendPulse adicionando seu ID de Cliente e Chave Secreta.
3. Faça o upload do plugin para o seu site WordPress e ative-o.

## Configuração

- Para configurar a integração com o SendPulse, obtenha seu ID de Cliente e Chave Secreta no SendPulse.
- Substitua os espaços reservados `"ADICIONAR AQUI O ID DE CLIENTE"` e `"ADICIONAR AQUI A CHAVE SECRETA"` na função `esi_get_sendpulse_token()` no arquivo `esi-integration.php` pelo seu ID de Cliente e Chave Secreta reais.

## Uso

1. Crie um formulário no Elementor com os seguintes campos obrigatórios. A consulta dessas variáveis é baseada no ID cadastrado:
   - 'name' (para o nome do usuário)
   - 'email' (para o endereço de e-mail do usuário)
   - 'phone' (opcional, para o número de telefone do usuário)
   - 'lista' (obrigatório, para especificar a lista de e-mails do SendPulse)
   - Você pode adicionar outras variáveis diretamente no código para complementar a integração

2. Após criar o formulário no Elementor, adicione o link do código no webhook em "função após envio" do Elementor:

Quando os usuários enviarem o formulário em seu site, os dados serão enviados automaticamente para a lista de e-mails do SendPulse que você especificou.
