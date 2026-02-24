import re
import sys
from pathlib import Path


def normalize_md_to_text(markdown: str) -> list[str]:
    out: list[str] = []
    for raw in markdown.splitlines():
        line = raw.rstrip()

        if not line:
            out.append("")
            continue

        if line.startswith("```"):
            out.append("")
            continue

        if line.startswith("#"):
            level = len(line) - len(line.lstrip("#"))
            title = line[level:].strip()
            if level <= 2:
                out.append(title.upper())
                out.append("-" * min(max(len(title), 8), 80))
            else:
                out.append(title)
            continue

        s = line.lstrip()
        if s.startswith("- "):
            out.append("* " + s[2:])
            continue

        if re.match(r"^\d+\.\s+", s):
            out.append(s)
            continue

        out.append(s)

    return out


def wrap_line(text: str, width: int = 96) -> list[str]:
    if not text:
        return [""]

    words = text.split()
    lines: list[str] = []
    current = words[0]
    for w in words[1:]:
        if len(current) + 1 + len(w) <= width:
            current += " " + w
        else:
            lines.append(current)
            current = w
    lines.append(current)
    return lines


def escape_pdf_text(text: str) -> str:
    return text.replace("\\", "\\\\").replace("(", "\\(").replace(")", "\\)")


def build_pdf(text_lines: list[str], output_path: Path) -> None:
    page_width = 595
    page_height = 842
    left = 45
    top = 800
    font_size = 10
    line_height = 13
    lines_per_page = 56

    wrapped: list[str] = []
    for line in text_lines:
        wrapped.extend(wrap_line(line))

    pages: list[list[str]] = []
    for i in range(0, len(wrapped), lines_per_page):
        pages.append(wrapped[i : i + lines_per_page])

    if not pages:
        pages = [["(empty)"]]

    objects: dict[int, bytes] = {}
    objects[1] = b"<< /Type /Catalog /Pages 2 0 R >>"
    objects[3] = b"<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>"

    page_ids: list[int] = []
    obj_id = 4
    for page_lines in pages:
        content_id = obj_id
        page_id = obj_id + 1
        obj_id += 2
        page_ids.append(page_id)

        stream_lines = [
            "BT",
            f"/F1 {font_size} Tf",
            f"{line_height} TL",
            f"{left} {top} Td",
        ]
        for ln in page_lines:
            esc = escape_pdf_text(ln)
            stream_lines.append(f"({esc}) Tj")
            stream_lines.append("T*")
        stream_lines.append("ET")
        stream = "\n".join(stream_lines).encode("latin-1", errors="replace")

        content_obj = (
            f"<< /Length {len(stream)} >>\nstream\n".encode("ascii")
            + stream
            + b"\nendstream"
        )
        page_obj = (
            f"<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {page_width} {page_height}] "
            f"/Resources << /Font << /F1 3 0 R >> >> /Contents {content_id} 0 R >>"
        ).encode("ascii")

        objects[content_id] = content_obj
        objects[page_id] = page_obj

    kids = " ".join(f"{pid} 0 R" for pid in page_ids)
    objects[2] = f"<< /Type /Pages /Count {len(page_ids)} /Kids [ {kids} ] >>".encode(
        "ascii"
    )

    max_obj = max(objects.keys())
    offsets = [0] * (max_obj + 1)
    content = bytearray()
    content.extend(b"%PDF-1.4\n%\xe2\xe3\xcf\xd3\n")

    for oid in range(1, max_obj + 1):
        offsets[oid] = len(content)
        content.extend(f"{oid} 0 obj\n".encode("ascii"))
        content.extend(objects[oid])
        content.extend(b"\nendobj\n")

    xref_offset = len(content)
    content.extend(f"xref\n0 {max_obj + 1}\n".encode("ascii"))
    content.extend(b"0000000000 65535 f \n")
    for oid in range(1, max_obj + 1):
        content.extend(f"{offsets[oid]:010} 00000 n \n".encode("ascii"))

    trailer = (
        f"trailer\n<< /Size {max_obj + 1} /Root 1 0 R >>\n"
        f"startxref\n{xref_offset}\n%%EOF\n"
    ).encode("ascii")
    content.extend(trailer)

    output_path.write_bytes(content)


def main() -> int:
    if len(sys.argv) != 3:
        print("Usage: python scripts/md_to_simple_pdf.py <input.md> <output.pdf>")
        return 1

    input_md = Path(sys.argv[1])
    output_pdf = Path(sys.argv[2])
    markdown = input_md.read_text(encoding="utf-8")
    text_lines = normalize_md_to_text(markdown)
    build_pdf(text_lines, output_pdf)
    print(f"PDF creado: {output_pdf}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

