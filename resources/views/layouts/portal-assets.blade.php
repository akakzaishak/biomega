<title>{{ $title ?? 'TronSport Medicamon | Portal' }}</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
<script>
  tailwind.config = {
    darkMode: 'class',
    theme: {
      extend: {
        colors: {
          primary: '#005ea4',
          'primary-container': '#0077ce',
          surface: '#f8f9fa',
          'surface-container-lowest': '#ffffff',
          'surface-container-low': '#f3f4f5',
          'surface-container': '#edeeef',
          'on-surface': '#191c1d',
          'on-surface-variant': '#404752',
          outline: '#707783',
          'outline-variant': '#c0c7d4',
          tertiary: '#186a22',
          error: '#ba1a1a',
          'error-container': '#ffdad6',
          'on-error-container': '#93000a',
          'on-primary': '#ffffff',
        },
        fontFamily: {
          headline: ['Manrope'],
          body: ['Inter'],
        },
      },
    },
  }
</script>
<style>
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
  body { font-family: 'Inter', sans-serif; }
  .headline { font-family: 'Manrope', sans-serif; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
  .fade-in { animation: fadeIn 0.4s ease both; }
  @keyframes shake { 0%,100% { transform: translateX(0); } 20% { transform: translateX(-6px); } 40% { transform: translateX(6px); } 60% { transform: translateX(-4px); } 80% { transform: translateX(4px); } }
  .shake { animation: shake 0.4s ease; }
</style>