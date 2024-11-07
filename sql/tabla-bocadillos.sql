CREATE TABLE bocadillos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(5, 2) NOT NULL
);
INSERT INTO bocadillos (nombre, descripcion, precio) VALUES
('Tortilla Española', 'Un clásico nacional reconocible en todo el mundo. Nuestro bocadillo de tortilla es uno de los más solicitados por los clientes, tanto los de toda la vida como los recién llegados.', 4.50),
('Bocadillo de Flamenquín', 'Rollito empanado de carne picada con jamón cocido, acompañado de tomate natural en rodajas. Pruébalo también con nuestra exquisita mayonesa casera.', 5.20),
('Bocadillo de Pollo Empanado', 'Deliciosos filetes de pollo empanado frito. Crujiente y sabroso, disfrútalo aún más con nuestra mayonesa casera.', 5.00),
('Bocadillo de Perrito', 'Salchicha de cerdo embutida, cocinada con nuestra salsa de tomate especial. El clásico entre los clásicos, es el bocadillo más popular entre adultos, jóvenes y niños.', 3.80),
('Bocadillo Africano', 'Huevo duro, anchoas y mayonesa. Otro de los “clásicos” preferidos por nuestros clientes más asiduos. Esta original combinación de ingredientes te sorprenderá por su exquisito sabor y textura.', 6.00),
('Bocadillo de Alcachofas', 'Cuartos de alcachofa acompañados de anchoas y mayonesa. Este bocadillo, que nunca falta en el menú de nuestra clientela más veterana, sorprende a los clientes más nuevos por su delicioso sabor y su ligereza', 4.90),
('Montijano', 'Nuestra versión del “serranito”: tomate natural, lomo de cerdo, jamón serrano y pimientos verdes fritos. No podía faltar en nuestra carta uno de los bocadillos más reconocibles de la gastronomía andaluza, con una combinación generosa de ingredientes frescos y locales.', 5.30),
('Bocadillo de Habas con Jamón', 'El plato estrella de la gastronomía granadina: deliciosas habas fritas con jamón serrano. No podrás irte sin probar uno de nuestros bocadillos más tradicionales', 4.80),
('Bocadillo de Chorizo Frito', 'El bocadillo perfecto para los que buscan algo más contundente. Ideal para tomarlo a mediodía acompañado de una cerveza o refresco bien fríos.', 4.60),
('Bocadillo Nivero', 'Picada de chorizo con queso brie. Nuestra última incorporación para los más atrevidos, es el ganador del Concurso 75º Aniversario Bar Aliatar. Debe su nombre a la población de Nívar, en las afueras de Granada.', 5.50),
('Bocadillo San Francisco', 'El “clásico moderno” de nuestra carta: lomo de cerdo, panceta natural, queso gouda fundido y lechuga acompañados (o no) de nuestra deliciosa mayonesa casera. Es el bocadillo estrella para nuestros clientes más jóvenes, y algunos que ya no lo son tanto. Pídelo a cualquier hora del día.', 4.70),
('Bocadillo de Morcilla', 'Sabrosa y especiada, no puedes irte sin probar uno de los secretos de nuestro éxito. El bocadillo perfecto para los que buscan algo tradicional a la par que sencillo.', 5.10);