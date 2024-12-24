namespace Models;

public class Dienst : IInschrijfService
{
    private readonly Dictionary<string, List<string>> _inschrijvingen = new();

    public string SchrijfIn(string naam, string sport)
    {
        if (!_inschrijvingen.ContainsKey(sport))
        {
            _inschrijvingen[sport] = new List<string>();
        }

        if (_inschrijvingen[sport].Contains(naam))
        {
            return $"{naam} is al ingeschreven voor {sport}.";
        }

        _inschrijvingen[sport].Add(naam);
        return $"{naam} is succesvol ingeschreven voor {sport}.";
    }

    public Dictionary<string, List<string>> GetInschrijvingen()
    {
        return _inschrijvingen;
    }
}
