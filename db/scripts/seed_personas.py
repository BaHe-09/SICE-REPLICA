from faker import Faker
import random
import string

fake = Faker("es_MX")


# ==============================
# GENERADOR DE CURP
# ==============================
def generar_curp(nombre, ap_pat, ap_mat, fecha, index):
    letras = (ap_pat[:1] + ap_mat[:1] + nombre[:1]).upper()
    fecha_str = fecha.strftime("%y%m%d")

    estado = random.choice([
        "AS","BC","BS","CC","CL","CM","CS","CH","DF","DG",
        "GT","GR","HG","JC","MC","MN","MS","NT","NL","OC",
        "PL","QT","QR","SP","SL","SR","TC","TS","TL","VZ",
        "YN","ZS",
    ])

    sexo = random.choice(["H", "M"])

    random_chars = "".join(random.choices(string.ascii_uppercase + string.digits, k=4))

    # 👇 IMPORTANTE: agregamos index para evitar duplicados
    curp = f"{letras}{fecha_str}{sexo}{estado}{random_chars}{index%10}{random.randint(0,9)}"

    return curp[:18]


# ==============================
# SEEDER PRINCIPAL
# ==============================
def seed_personas(cursor, conn, total=200):

    print("\n--- MODULO PERSONAS ---\n")

    try:
        # ==============================
        # GENEROS
        # ==============================
        generos = ["Masculino", "Femenino", "No especificado"]

        for g in generos:
            cursor.execute("""
                INSERT IGNORE INTO genero(nombre_genero)
                VALUES(%s)
            """, (g,))

        conn.commit()

        # Obtener IDs de género
        cursor.execute("SELECT id_genero FROM genero")
        generos_ids = [g[0] for g in cursor.fetchall()]

        print("Generos cargados:", generos_ids)

        if not generos_ids:
            raise Exception("❌ No hay géneros en la base de datos")

        # ==============================
        # PERSONAS
        # ==============================
        for i in range(total):
            try:
                print(f"➡️ Insertando persona {i+1}/{total}")

                nombre = fake.first_name()
                ap_pat = fake.last_name()
                ap_mat = fake.last_name()

                fecha_nac = fake.date_between(start_date="-60y", end_date="-17y")
                genero = random.choice(generos_ids)

                curp = generar_curp(nombre, ap_pat, ap_mat, fecha_nac, i)

                # PERSONA
                cursor.execute("""
                    INSERT INTO persona
                    (
                        nombre,
                        apellido_paterno,
                        apellido_materno,
                        curp,
                        fecha_nacimiento,
                        id_genero
                    )
                    VALUES (%s,%s,%s,%s,%s,%s)
                """, (nombre, ap_pat, ap_mat, curp, fecha_nac, genero))

                id_persona = cursor.lastrowid

                # TELEFONO
                cursor.execute("""
                    INSERT INTO persona_telefono
                    (id_persona, telefono)
                    VALUES (%s,%s)
                """, (id_persona, fake.phone_number()))

                # CORREO
                cursor.execute("""
                    INSERT INTO persona_correo
                    (id_persona, correo)
                    VALUES (%s,%s)
                """, (id_persona, fake.email()))

                # DIRECCION
                cursor.execute("""
                    INSERT INTO persona_direccion
                    (id_persona, direccion)
                    VALUES (%s,%s)
                """, (id_persona, fake.address()))

                # Commit cada 50 (mejor rendimiento)
                if i % 50 == 0:
                    conn.commit()

            except Exception as e:
                print(f"❌ Error en persona {i+1}: {e}")
                continue

        conn.commit()
        print(f"\n✅ {total} personas generadas correctamente\n")

    except Exception as e:
        print("\n💥 Error general en seed_personas:", e)

    finally:
        print("🔒 Seeder de personas finalizado\n")