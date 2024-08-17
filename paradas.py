import requests
import mysql.connector
from mysql.connector import Error

# URL del servicio
url = "https://itranvias.com/queryitr_v3.php?func=7&dato=20160101T000000_gl_0_20160101T000000"

# Hacer la petición a la API
response = requests.get(url)

# Comprobar si la petición fue exitosa
if response.status_code == 200:
    data = response.json()  # Convertir la respuesta a JSON
    try:
        # Acceder a las paradas
        paradas = data["iTranvias"]["actualizacion"]["paradas"]

        # Conectar a la base de datos MySQL
        conn = mysql.connector.connect(
            host='localhost',       # Cambia esto si tu base de datos está en otro servidor
            database='busgalicia',  # Nombre de tu base de datos
            user='root',      # Tu usuario de MySQL
            password='' # Tu contraseña de MySQL
        )

        if conn.is_connected():
            cursor = conn.cursor()

            # Crear la tabla si no existe
            cursor.execute('''
                CREATE TABLE IF NOT EXISTS paradas (
                    id INT PRIMARY KEY,
                    nombre VARCHAR(255) NOT NULL,
                    posx DECIMAL(10, 8) NOT NULL,
                    posy DECIMAL(10, 8) NOT NULL,
                    enlaces TEXT
                )
            ''')

            # Insertar cada parada en la base de datos
            for parada in paradas:
                # Convertir la lista de enlaces a una cadena
                enlaces_str = ', '.join(map(str, parada['enlaces']))
                cursor.execute('''
                    INSERT INTO paradas (id, nombre, posx, posy, enlaces) 
                    VALUES (%s, %s, %s, %s, %s)
                    ON DUPLICATE KEY UPDATE 
                    nombre=%s, posx=%s, posy=%s, enlaces=%s
                ''', (parada['id'], parada['nombre'], parada['posx'], parada['posy'], enlaces_str,
                      parada['nombre'], parada['posx'], parada['posy'], enlaces_str))

            # Guardar los cambios y cerrar la conexión
            conn.commit()
            print("Datos insertados correctamente en la base de datos.")
            
    except KeyError as e:
        print(f"Error al acceder a la clave: {e}")

    except Error as e:
        print(f"Error al conectar a MySQL: {e}")

    finally:
        if conn.is_connected():
            cursor.close()
            conn.close()
else:
    print(f"Error en la petición: {response.status_code}")
