import requests
import mysql.connector
import time
import json

# Conexión a la base de datos
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="coruna"
)

cursor = db.cursor()

# Obtener IDs de líneas de la tabla lineas
cursor.execute("SELECT id FROM lineas")
lineas = cursor.fetchall()  # Obtiene todos los IDs de líneas
lineas_ids = [linea[0] for linea in lineas]  # Convierte a lista de IDs

# Función para insertar rutas
def insertar_ruta(ruta_id, linea_id, sentido, paradas, destino, comentarios, forma):
    sql = """
    INSERT INTO rutas (id, linea_id, sentido, paradas, destino, comentarios, forma) 
    VALUES (%s, %s, %s, %s, %s, %s, %s)
    """
    cursor.execute(sql, (ruta_id, linea_id, sentido, paradas, destino, comentarios, forma))
    db.commit()

# Obtener rutas de cada línea y sentido
for linea_id in lineas_ids:
    for sentido in range(3):  # Cambia el rango según los sentidos disponibles
        url = f"https://itranvias.com/queryitr_v3.php?func=99&dato={linea_id}&mostrar=PR"
        response = requests.get(url)
        
        if response.status_code == 200:
            data = response.json()
            if data.get("resultado") == "OK":
                for mapa in data.get("mapas", []):  # Usa get para evitar KeyError
                    # Imprimir mapa para inspección
                    print(json.dumps(mapa, indent=2))  # Imprimir el mapa con formato
                    
                    # Obtener las paradas y el recorrido del sentido correspondiente
                    for sentido_data in mapa.get("paradas", []):  # Usa get para evitar KeyError
                        if sentido_data.get("sentido") == str(sentido):
                            paradas = json.dumps([parada.get("id") for parada in sentido_data.get("paradas", [])])  # Usa get para evitar KeyError
                            
                            # Crear el ID de la ruta basado en linea_id y sentido
                            ruta_id = f"{linea_id}{str(sentido).zfill(2)}"  # Añadir un 0 delante del sentido
                            
                            # Obtener el recorrido de la sección de recorridos
                            forma = None
                            for recorrido_data in mapa.get("recorridos", []):  # Obtener el recorrido
                                if recorrido_data.get("sentido") == str(sentido):
                                    forma = recorrido_data.get("recorrido")  # Obtener el recorrido
                                    break

                            # Si no se encuentra el recorrido, usar un valor por defecto
                            if forma is None:
                                forma = "Sin forma"

                            # Insertar en la base de datos con destino y comentarios como NULL
                            insertar_ruta(ruta_id, linea_id, sentido, paradas, None, None, forma)

            else:
                print(f"Error en la respuesta para línea {linea_id}: {data.get('resultado')}")
        else:
            print(f"Error al hacer la petición para línea {linea_id}: {response.status_code}")

        # Esperar 15 segundos antes de la siguiente petición
        time.sleep(15)

# Cerrar conexión a la base de datos
cursor.close()
db.close()
