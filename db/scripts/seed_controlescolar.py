import random
from faker import Faker

fake = Faker("es_MX")


def generar_numero_control(anio, contador):
    return f"{anio}66{contador:04d}"


def seed_control_escolar(cursor, conn, total_alumnos=1500):

    print("\n--- MODULO CONTROL ESCOLAR ---\n")

    try:
        # ==============================
        # VALIDACIONES
        # ==============================
        cursor.execute("SELECT COUNT(*) FROM persona")
        total_personas = cursor.fetchone()[0]

        if total_personas == 0:
            raise Exception("❌ No hay personas registradas")

        cursor.execute("SELECT id_carrera FROM carrera")
        carreras = [c[0] for c in cursor.fetchall()]

        if not carreras:
            raise Exception("❌ No hay carreras registradas")

        # ==============================
        # PERSONAS
        # ==============================
        cursor.execute("""
            SELECT id_persona
            FROM persona
            LIMIT %s
        """, (total_alumnos,))

        personas = [p[0] for p in cursor.fetchall()]

        print(f"👥 Personas seleccionadas: {len(personas)}")

        # ==============================
        # ALUMNOS
        # ==============================
        contador = 1

        for i, persona in enumerate(personas):

            try:
                if i % 100 == 0:
                    print(f"➡️ Alumno {i+1}/{len(personas)}")

                fecha_ingreso = fake.date_between(start_date="-5y", end_date="-1y")
                anio = str(fecha_ingreso.year)[2:]

                numero_control = generar_numero_control(anio, contador)

                cursor.execute("""
                    INSERT IGNORE INTO alumno
                    (
                        id_persona,
                        numero_control,
                        id_carrera,
                        fecha_ingreso,
                        semestre_actual
                    )
                    VALUES (%s,%s,%s,%s,%s)
                """, (
                    persona,
                    numero_control,
                    random.choice(carreras),
                    fecha_ingreso,
                    random.randint(1, 9)
                ))

                contador += 1

            except Exception as e:
                print(f"❌ Error alumno {i}: {e}")
                continue

        conn.commit()

        # ==============================
        # INSCRIPCIONES
        # ==============================
        cursor.execute("SELECT id_alumno FROM alumno")
        alumnos = [a[0] for a in cursor.fetchall()]

        cursor.execute("SELECT id_grupo FROM grupo")
        grupos = [g[0] for g in cursor.fetchall()]

        if not grupos:
            raise Exception("❌ No hay grupos")

        for i, alumno in enumerate(alumnos):

            materias = random.sample(grupos, min(len(grupos), random.randint(4, 6)))

            for grupo in materias:
                cursor.execute("""
                    INSERT IGNORE INTO inscripcion
                    (
                        id_alumno,
                        id_grupo,
                        fecha_inscripcion,
                        estatus
                    )
                    VALUES (%s,%s,%s,%s)
                """, (
                    alumno,
                    grupo,
                    fake.date_between(start_date="-2y", end_date="today"),
                    random.choice(["inscrito", "aprobado", "reprobado"])
                ))

            if i % 200 == 0:
                conn.commit()

        conn.commit()

        # ==============================
        # EVALUACIONES
        # ==============================
        evaluaciones_por_grupo = {}

        for grupo in grupos:

            cursor.execute("""
                SELECT id_evaluacion FROM evaluacion WHERE id_grupo = %s
            """, (grupo,))

            existentes = cursor.fetchall()

            if existentes:
                evaluaciones_por_grupo[grupo] = [e[0] for e in existentes]
                continue

            evaluaciones = [("Parcial 1", 30), ("Parcial 2", 30), ("Parcial 3", 40)]

            ids = []

            for e in evaluaciones:
                cursor.execute("""
                    INSERT INTO evaluacion (id_grupo,nombre,porcentaje)
                    VALUES (%s,%s,%s)
                """, (grupo, e[0], e[1]))

                ids.append(cursor.lastrowid)

            evaluaciones_por_grupo[grupo] = ids

        conn.commit()

        # ==============================
        # CALIFICACIONES
        # ==============================
        cursor.execute("""
            SELECT id_inscripcion,id_grupo
            FROM inscripcion
        """)

        inscripciones = cursor.fetchall()

        for i, (id_ins, grupo) in enumerate(inscripciones):

            evaluaciones = evaluaciones_por_grupo.get(grupo, [])

            for ev in evaluaciones:
                cursor.execute("""
                    INSERT IGNORE INTO calificacion
                    (id_inscripcion,id_evaluacion,calificacion)
                    VALUES (%s,%s,%s)
                """, (
                    id_ins,
                    ev,
                    round(random.uniform(50, 100), 2)
                ))

            if i % 300 == 0:
                conn.commit()

        conn.commit()

        print("\n✅ Módulo control escolar sembrado correctamente\n")

    except Exception as e:
        print("\n💥 Error en control escolar:", e)