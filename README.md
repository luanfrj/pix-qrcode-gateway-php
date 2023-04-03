# Gateway para pagamentos com QR-CODE PIX

## Requisitos
Para que funcione o gateway precisa de um servidor com o PHP e o Banco de Dados MYSQL instalado.
Também é preciso que o usuário do Mercado Pago esteja configurado e já exista o STORE e POS, conforme documentação em:
https://www.mercadopago.com.br/developers/pt/docs/qr-code/pre-requisites/glossary

https://www.mercadopago.com.br/developers/pt/reference/stores/_users_user_id_stores/post

https://www.mercadopago.com.br/developers/pt/reference/pos/_pos/post

https://www.mercadopago.com.br/developers/pt/reference/qr-dynamic/_instore_orders_qr_seller_collectors_user_id_pos_external_pos_id_qrs/post

https://www.mercadopago.com.br/developers/pt/reference

## Criação do banco de dados
O banco de dados precisa ter a tabela `order_data`, conforme mostrado abaixo. O script encontra-se no arquivo `create_table.sql`.

```sql
CREATE TABLE order_data (
    external_id INTEGER NOT NULL UNIQUE,
    order_status INTEGER NOT NULL,
    last_update TIMESTAMP
);
```

## Configuração da aplicação
A configuração da aplicação é feita usando o arquivo `gateway_config.ini`. Para gerá-lo, ajuste as configurações no arquivo `gateway_config_example.ini` e renomeie para `gateway_config.ini`.

As seguintes informações precisam ser informadas:

```ini
[database]
host = localhost        # Endereço do Banco de Dados
dbname =                # Nome do Banco de dados
username =              # Usuário do Banco de Dados
password =              # Senha do Banco de Dados

[pix]
wbhook_url = https://exemplo.com/pix-gateway/v1/webhook     # URL do Webhook
qrcode_duration = 10                                        # Tempo de duração do QR Code em minutos
token =                                                     # Token de acesso à api do Mercado Pago
user_id =                                                   # User ID do mercado pago
external_pos_id =                                           # Pos ID do mercado pago
```
