<?php
/*
Plugin Name: Elementor SendPulse Integration
Version: 1.0
Description: Integrate Elementor forms with SendPulse.
Author: github.com/wagnerschaider
*/

// Ativação do plugin
register_activation_hook(__FILE__, 'esi_activate_plugin');

function esi_activate_plugin()
{
    // Código de ativação, se necessário
}

// Função para obter o token de autorização do SendPulse
function esi_get_sendpulse_token()
{
    $token_request = array(
        "grant_type" => "client_credentials",
        "client_id" => "ADICIONAR AQUI O ID DE CLIENTE",
        "client_secret" => "ADICIONAR AQUI A CHAVE SECRETA"
    );

    $response = wp_remote_post(
        'https://api.sendpulse.com/oauth/access_token',
        array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($token_request)
        )
    );

    if (!is_wp_error($response)) {
        $response_body = wp_remote_retrieve_body($response);
        $response_data = json_decode($response_body, true);
        return $response_data['access_token'];
    } else {
        return false;
    }
}

// Função para lidar com o envio do formulário
function esi_handle_form_submission($response, $record)
{
     // Obter o token de autorização
     $sendpulse_token = esi_get_sendpulse_token();

     if (!$sendpulse_token) {
        // Lidar com a falha na obtenção do token
        $response_data = array(
            "success" => false,
            "data" => array(
                "message" => "Erro ao obter token",
                "errors" => array(),
                "data" => array()   
            )
        );
     } else {
      // Obter os campos do formulário
      $form_fields = $record->get( 'fields' );

      // Verificar se os campos estão presentes
      if (isset($form_fields['name'], $form_fields['email'])) {
          // Obtém os valores dos campos com base em seus IDs
          $name = isset($form_fields['name']['value']) ? $form_fields['name']['value'] : '';
          $email = isset($form_fields['email']['value']) ? $form_fields['email']['value'] : '';
          $lista_id = isset($form_fields['lista']['value']) ? $form_fields['lista']['value'] : '';

          // Garantir que a variável lista está presente
          if (!empty($lista_id)) {
              $sendpulse_payload = array(
                  "emails" => array(
                      array(
                          "email" => $email,
                          "variables" => array(
                              "name" => $name,
                              "phone" => $form_fields['phone']['value'] // Supondo que você também tenha um campo 'phone'. Você pode adicionar outras variáveis para importação
                          )
                      )
                  )
              );

              // Construir a URL com o ID da lista
              $sendpulse_api_url = "https://api.sendpulse.com/addressbooks/{$lista_id}/emails";

              // Use a função wp_remote_post() para fazer a solicitação à API do SendPulse
              $response = wp_remote_post(
                  $sendpulse_api_url,
                  array(
                      'headers' => array(
                          'Authorization' => "Bearer $sendpulse_token",
                          'Content-Type' => 'application/json'
                      ),
                      'body' => json_encode($sendpulse_payload)
                  )
              );

              if (!is_wp_error($response)) {
                  $response_code = wp_remote_retrieve_response_code($response);

                  if ($response_code === 200) {
                      // Resposta bem-sucedida
                      $response_data = array(
                          "success" => true,
                          "data" => array(
                              "message" => "Dados enviados com sucesso!",
                              "errors" => array(),
                              "data" => array()
                          )
                      );
                  } else {
                      // Tratar outras respostas
                  }
              } else {
                  // Lidar com campos ausentes ou formato incorreto
                  $response_data = array(
                      "success" => false,
                      "data" => array(
                          "message" => "Campos ausentes ou formato inválido",
                          "errors" => array(),
                          "data" => array()
                      )
                  );
              }
          } else {
              // Lidar com o caso em que o campo "lista" está vazio
              $response_data = array(
                  "success" => false,
                  "data" => array(
                      "message" => "ID para lista ausente",
                      "errors" => array(),
                      "data" => array()
                  )
              );
          }
      } else {
          // Lidar com campos ausentes
          $response_data = array(
              "success" => false,
              "data" => array(
                  "message" => "Campos obrigatórios ausentes",
                  "errors" => array(),
                  "data" => array()
              )
          );
      }
   }

  // Enviar apenas uma resposta JSON
  echo json_encode($response_data);
  exit; 

}

// Adicione ação para lidar com o envio do formulário do Elementor
add_action('elementor_pro/forms/webhooks/response', 'esi_handle_form_submission', 10, 2);

?>