CREATE TABLE "color" (
    "id"            INTEGER NOT NULL PRIMARY KEY,
    "name"          TEXT NOT NULL,
    "leader"        TEXT NOT NULL
);

CREATE TABLE "fest" (
    "id"            INTEGER NOT NULL PRIMARY KEY,
    "name"          TEXT NOT NULL,
    "start_at"      INTEGER NOT NULL,
    "end_at"        INTEGER NOT NULL
);

CREATE TABLE "team" (
    "fest_id"       INTEGER NOT NULL REFERENCES "fest" ( "id" ),
    "color_id"      INTEGER NOT NULL REFERENCES "color" ( "id" ),
    "name"          TEXT NOT NULL,
    "keyword"       TEXT NOT NULL,
    PRIMARY KEY ( "fest_id", "color_id" )
);

CREATE TABLE "official_data" (
    "id"            INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "fest_id"       INTEGER NOT NULL REFERENCES "fest" ( "id" ),
    "sha256sum"     TEXT NOT NULL,
    "downloaded_at" INTEGER NOT NULL
);

CREATE TABLE "official_win_data" (
    "data_id"       INTEGER NOT NULL REFERENCES "official_data" ( "id" ),
    "color_id"      INTEGER NOT NULL REFERENCES "color" ( "id" ),
    "count"         INTEGER NOT NULL,
    PRIMARY KEY ( "data_id", "color_id" )
);

INSERT INTO "color" VALUES
    ( 1, 'red', 'アオリ' ),
    ( 2, 'green', 'ホタル' );

INSERT INTO "fest" VALUES
    ( 1, 'ごはん vs パン', 1434186000, 1434272400 ),
    ( 2, '赤いきつね vs 緑のたぬき', 1435903200, 1435989600 ),
    ( 3, 'レモンティー vs ミルクティー', 1437804000, 1437890400 );

INSERT INTO "team" VALUES
    ( 1, 1, 'ごはん', 'ごはん' ),
    ( 1, 2, 'パン', 'パン' ),
    ( 2, 1, '赤いきつね', '赤いきつね' ),
    ( 2, 2, '緑のたぬき', '緑のたぬき' ),
    ( 3, 1, 'レモンティー', 'レモン' ),
    ( 3, 2, 'ミルクティー', 'ミルク' );
