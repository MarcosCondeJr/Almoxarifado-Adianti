CREATE TABLE grupo_material (
	id_grupomaterial SERIAL NOT NULL,
	cd_grupomaterial INT NOT NULL,
	nm_grupomaterial VARCHAR NOT NULL,
	ds_grupomaterial VARCHAR NOT NULL,
	CONSTRAINT pk_grupomaterial PRIMARY KEY(id_grupomaterial)
);

CREATE TABLE unidade_medida (
	id_unidademedida SERIAL NOT NULL,
	nm_unidademedida VARCHAR,
	CONSTRAINT pk_unidademedida PRIMARY KEY(id_unidademedida)
); 

create table material (
	id_material SERIAL NOT NULL,
	cd_material SERIAL NOT NULL,
	nm_material VARCHAR NOT NULL,
	qtd_estoque INT,
	id_unidademedida int NOT NULL,
	id_grupomaterial int NOT NULL,
	vl_medio DECIMAL,
	CONSTRAINT pk_material PRIMARY KEY(id_material),
	
	CONSTRAINT fk_unidademedida FOREIGN KEY(id_unidademedida)
	REFERENCES unidade_medida (id_unidademedida),

	CONSTRAINT fk_grupomaterial FOREIGN KEY(id_grupomaterial)
	REFERENCES grupo_material (id_grupomaterial)
);
