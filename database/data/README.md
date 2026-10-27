# Map data

| File | What | Source |
|---|---|---|
| `sale-boundary.geojson` | Municipal boundary of Salé (GeoJSON Polygon, `[lng, lat]`), simplified to about 30 m | © OpenStreetMap contributors, [ODbL](https://opendatacommons.org/licenses/odbl/). Relation [2801066](https://www.openstreetmap.org/relation/2801066), fetched 2026-10-27 through Nominatim (`polygon_threshold=0.0003`) |

`LocationResolver` uses a city's boundary to decide which pins belong to it. To add a city, fetch its administrative boundary the same way and store it in `cities.boundary`.
