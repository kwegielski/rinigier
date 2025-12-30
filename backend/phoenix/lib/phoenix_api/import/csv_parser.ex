defmodule PhoenixApi.Import.CsvParser do
  NimbleCSV.define(CSV, separator: ",", escape: "\"")

  def load_top_first_names(path, limit \\ 100) do
    path
    |> File.stream!()
    |> CSV.parse_stream(skip_headers: true)
    |> Enum.map(fn [name, _sex, count] ->
      {normalize(name), String.to_integer(count)}
    end)
    |> Enum.sort_by(fn {_name, count} -> -count end)
    |> Enum.take(limit)
    |> Enum.map(&elem(&1, 0))
  end

  def load_top_last_names(path, limit \\ 100) do
    path
    |> File.stream!()
    |> CSV.parse_stream(skip_headers: true)
    |> Enum.reduce(%{}, fn [_voivodeship, last_name, count], acc ->
      Map.update(
        acc,
        normalize(last_name),
        String.to_integer(count),
        &(&1 + String.to_integer(count))
      )
    end)
    |> Enum.sort_by(fn {_name, count} -> -count end)
    |> Enum.take(limit)
    |> Enum.map(&elem(&1, 0))
  end

  defp normalize(value) do
    value
    |> String.trim()
    |> String.downcase()
    |> String.capitalize()
  end
end