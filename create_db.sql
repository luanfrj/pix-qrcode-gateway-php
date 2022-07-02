CREATE TABLE order_data (
    external_id INTEGER NOT NULL UNIQUE,
    order_status INTEGER NOT NULL,
    last_update TIMESTAMP,
    external_pos_id VARCHAR (20)
);