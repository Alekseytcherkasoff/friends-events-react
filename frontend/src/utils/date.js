const pad = (n) => String(n).padStart(2, '0')

// Сегодняшняя дата в формате гггг-мм-дд (так её понимает <input type="date">)
export function todayIso() {
  const d = new Date()
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`
}

// гггг-мм-дд -> дд.мм.гггг (в таком виде дата хранится в базе)
export function isoToRu(iso) {
  const [year, month, day] = iso.split('-')
  return `${day}.${month}.${year}`
}

// Интервалы времени «00:00 - 01:00» … «23:00 - 24:00», как в старой форме
export const TIME_SLOTS = Array.from({ length: 24 }, (_, h) => `${pad(h)}:00 - ${pad(h + 1)}:00`)
