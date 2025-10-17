Design tokens & usage

Paleta SEMHYS
- Primary (semhys.500): #0F9B79
- Accent (accent.500): #F36C13
- Emerald (emerald.500): #6DBE45

Tokens (CSS variables)
- --semhys-primary: color principal
- --semhys-accent: acento
- --semhys-emerald: verde hoja
- spacing: --space-1..--space-4
- radii: --radius-sm, --radius-md
- shadows: --shadow-sm, --shadow-md

Component usage
- Button: import Button from 'src/components/Button'
  <Button variant="primary">Acción</Button>
  <Button variant="ghost">Secundaria</Button>

- Card: simple container con borde de color y título.

Tailwind
Usa las clases: bg-semhys-500, text-semhys-700, bg-accent-500, etc.
